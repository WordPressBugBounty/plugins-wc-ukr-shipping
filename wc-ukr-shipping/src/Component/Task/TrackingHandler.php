<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Task;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\DB\Repositories\TrackingRepository;

if ( ! defined('ABSPATH')) {
    exit;
}

class TrackingHandler implements TaskHandlerInterface
{
    private const CURSOR_KEY = 'wcus_tracking_cursor_id';

    private TrackingRepository $trackingRepository;
    private SmartyParcelApi $smartyParcelApi;

    public function __construct(
        TrackingRepository $trackingRepository,
        SmartyParcelApi $smartyParcelApi
    ) {
        $this->trackingRepository = $trackingRepository;
        $this->smartyParcelApi = $smartyParcelApi;
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
        foreach ($trackings as $tracking) {
            $trackingNumbers[] = $tracking['tracking_number'];
            $trackingMap[$tracking['tracking_number']] = (int)$tracking['id'];
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
