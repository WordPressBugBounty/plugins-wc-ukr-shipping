<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules;

use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Services\AutomationService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\Facades\DB;
use WP_REST_Request;
use WP_REST_Response;

class WcusLegacyCompatibility implements ModuleInterface
{
    private AutomationService $automationService;

    public function __construct(AutomationService $automationService)
    {
        $this->automationService = $automationService;
    }

    public function init(): void
    {
        add_action('rest_api_init', [$this, 'registerTrackingCallback']);
    }

    public function registerTrackingCallback(): void
    {
        // todo: move to separated component
        register_rest_route('wc-ukr-shipping/v1', 'tracking', [
            'methods' => 'POST',
            'callback' => [$this, 'trackingHandler'],
            'permission_callback' => function (WP_REST_Request $request) {
                return true;
            }
        ]);

        if ((int)wc_ukr_shipping_get_option('wcus_legacy_pro_tracking') === 1
            && !empty(wc_ukr_shipping_get_option('wc_ukr_shipping_license_key'))) {
            register_rest_route('wc-ukr-shipping/v1', 'track', [
                'methods' => 'POST',
                'callback' => [$this, 'trackStatus'],
                'permission_callback' => function (WP_REST_Request $request) {
                    return true;
                }
            ]);
        }
    }

    public function trackStatus(WP_REST_Request $request): WP_REST_Response
    {
        $json = json_decode($request->get_body(), true);
        if (json_last_error() || empty($json['signature']) || empty($json['data'])) {
            return new WP_REST_Response([
                'success' => false,
                'errorMessage' => 'Bad request format',
            ], 400);
        }

        $key = wc_ukr_shipping_get_option('wc_ukr_shipping_license_key');
        if (empty($key)) {
            return new WP_REST_Response([
                'success' => false,
                'errorMessage' => 'Internal server error',
            ], 500);
        }

        // Compare signatures
        $signature = hash('sha256', "$key." . base64_encode(json_encode($json['data'] ?? [], JSON_UNESCAPED_UNICODE)));
        if ($signature !== $json['signature']) {
            return new WP_REST_Response([
                'success' => false,
                'errorMessage' => 'Request not valid',
            ], 400);
        }

        $data = $json['data'];
        try {
            $label = DB::table(DB::prefixedTable('wc_ukr_shipping_labels'))
                ->where('tracking_number', $data['tracking_number'])
                ->where('carrier_slug', 'wcus_pro')
                ->first();

            if ($label === null) {
                // Do nothing
                return new WP_REST_Response([
                    'success' => true,
                ], 200);
            }

            $label = (array)$label;
            if ($label['carrier_status_code'] !== $data['carrier_status']) {
                global $wpdb;
                $wpdb->update(
                    "{$wpdb->prefix}wc_ukr_shipping_labels",
                    [
                        'tracking_status' => $data['status'],
                        'carrier_status' => $data['carrier_status_additional'],
                        'carrier_status_code' => $data['carrier_status'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [ 'id' => $label['id'] ],
                    [ '%s', '%s', '%s', '%s' ],
                    [ '%d' ]
                );

                $this->automationService->executeEvent(
                    AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                    new Context(
                        AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                        wc_get_order((int)$label['order_id']),
                        [
                            'carrier_slug' => 'nova_poshta',
                            'tracking_number' => $label['tracking_number'],
                            'carrier_status_code' => $data['carrier_status'],
                            'carrier_status' => $data['carrier_status_additional'],
                        ]
                    )
                );
            }

            return new WP_REST_Response([
                'success' => true,
            ], 200);
        } catch (\Throwable $e) {
            return new WP_REST_Response([
                'success' => false,
                'errorMessage' => 'Internal server error',
            ], 500);
        }
    }

    public function trackingHandler(WP_REST_Request $request): WP_REST_Response
    {
        $secret = wc_ukr_shipping_get_option('wcus_smartyparcel_api_key');
        if (empty($secret)) {
            return new WP_REST_Response(null, 404);
        }

        $requestSignature = $request->get_header('WCUS-Signature');
        if (empty($requestSignature)) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Bad request format',
            ], 400);
        }

        $json = json_decode($request->get_body() ?? '', true);
        if (json_last_error()) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Bad request format',
            ], 400);
        }

        // Compare signatures
        $signature = hash('sha256', $secret . base64_encode(json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)));
        if ($signature !== $requestSignature) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Request not valid',
            ], 403);
        }

        $label = DB::table(DB::prefixedTable('wc_ukr_shipping_labels'))
            ->where('tracking_number', $json['tracking_number'])
            ->first();

        if ($label === null) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Label not found',
            ]);
        }

        $label = (array)$label;
        try {
            if ($label['carrier_status_code'] !== $json['carrier_status']) {
                global $wpdb;
                $wpdb->update(
                    "{$wpdb->prefix}wc_ukr_shipping_labels",
                    [
                        'tracking_status' => $json['status'],
                        'tracking_sub_status' => $json['sub_status'],
                        'carrier_status' => $json['carrier_status_description'],
                        'carrier_status_code' => $json['carrier_status'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [ 'tracking_number' => $json['tracking_number'] ],
                    [ '%s', '%s', '%s', '%s', '%s' ],
                    [ '%s' ]
                );

                $this->automationService->executeEvent(
                    AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                    new Context(
                        AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                        wc_get_order((int)$label['order_id']),
                        [
                            'carrier_slug' => $label['carrier_slug'] ?? '',
                            'tracking_number' => $label['tracking_number'],
                            'carrier_status_code' => $json['carrier_status'],
                            'carrier_status' => $json['carrier_status_description'],
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'Internal server error',
            ], 500);
        }

        return new WP_REST_Response([
            'success' => true,
        ]);
    }
}
