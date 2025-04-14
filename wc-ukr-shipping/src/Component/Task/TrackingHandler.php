<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Task;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\DB\Repositories\TrackingRepository;
use kirillbdev\WCUkrShipping\Services\AutomationService;

if ( ! defined('ABSPATH')) {
    exit;
}

class TrackingHandler implements TaskHandlerInterface
{
    private const CURSOR_KEY = 'wcus_tracking_cursor_id';

    private TrackingRepository $trackingRepository;
    private SmartyParcelApi $smartyParcelApi;
    private AutomationService $automationService;

    public function __construct(
        TrackingRepository $trackingRepository,
        SmartyParcelApi $smartyParcelApi,
        AutomationService $automationService
    ) {
        $this->trackingRepository = $trackingRepository;
        $this->smartyParcelApi = $smartyParcelApi;
        $this->automationService = $automationService;
    }

    public function handle(?TaskInterface $task): void
    {
        $apiKey = get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
        if (empty($apiKey)) {
            return;
        }

        $cursorId = (int)get_transient(self::CURSOR_KEY);
        if ($cursorId === 0) {
            $cursorId = null;
        }

        $trackings = $this->trackingRepository->findActiveTrackingRecords($cursorId, 100);
        if (count($trackings) === 0) {
            delete_transient(self::CURSOR_KEY);
            return;
        }

        $trackingNumbers = [];
        $trackingMap = [];
        $carrierStatusMap = [];
        $trackingFullMap = [];
        foreach ($trackings as $tracking) {
            $trackingNumbers[] = $tracking['tracking_number'];
            $trackingMap[$tracking['tracking_number']] = (int)$tracking['id'];
            $trackingFullMap[$tracking['tracking_number']] = $tracking;
            $carrierStatusMap[$tracking['tracking_number']] = $tracking['carrier_status_code'];
        }

        global $wpdb;
        try {
            $response = $this->smartyParcelApi->getTrackings($trackingNumbers);
            foreach ($response['data'] as $trackingItem) {
                $trackingNumber = $trackingItem['tracking_number'];
                if (!isset($trackingMap[$trackingNumber])) {
                    continue;
                }
                if ($carrierStatusMap[$trackingNumber] === $trackingItem['carrier_status']) {
                    continue;
                }

                $wpdb->update(
                    "{$wpdb->prefix}wc_ukr_shipping_labels",
                    [
                        'tracking_active' => $trackingItem['active'] ? 1 : 0,
                        'tracking_status' => $trackingItem['status'],
                        'tracking_sub_status' => $trackingItem['sub_status'],
                        'carrier_status' => $trackingItem['carrier_status_description'],
                        'carrier_status_code' => $trackingItem['carrier_status'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ],
                    [ 'id' => $trackingMap[$trackingNumber] ],
                    [ '%d', '%s', '%s', '%s', '%s', '%s' ],
                    [ '%d' ]
                );

                try {
                    $this->automationService->executeEvent(
                        AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                        new Context(
                            AutomationService::EVENT_SP_CARRIER_STATUS_CHANGED,
                            wc_get_order((int)$trackingFullMap[$trackingNumber]['order_id']),
                            [
                                'tracking_number' => $trackingNumber,
                                'carrier_status_code' => $trackingItem['carrier_status'],
                                'carrier_status' => $trackingItem['carrier_status_description'],
                            ]
                        )
                    );
                } catch (\Throwable $e) {
                    // Do nothing
                }
            }

            if (count($trackings) < 100) {
                delete_transient(self::CURSOR_KEY);
            } else {
                set_transient(self::CURSOR_KEY, max($trackingMap));
            }
        } catch (\Throwable $e) {
            if (function_exists('wc_get_logger')) {
                wc_get_logger()->error('[Tracking]' . $e->getMessage());
            }
        }
    }
}
