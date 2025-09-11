<?php

namespace kirillbdev\WCUkrShipping\DB\Mappers;

use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;

if ( ! defined('ABSPATH')) {
    exit;
}

class OrderListMapper
{
    /**
     * @param array $data
     * @return array
     */
    public function fetchOrders($data)
    {
        $orders = [];

        foreach ($data as $item) {
            $order = [
                'id' => (int)$item['id'],
                'selected' => false,
                'label_id' => $item['label_id'],
                'label_carrier_slug' => $item['carrier_slug'],
                'label_db_id' => (int)$item['label_db_id'],
                'tracking_number' => $item['tracking_number'],
                'tracking_status' => $item['tracking_status'],
                'carrier_status_code' => $item['carrier_status_code'],
                'carrier_status' => $item['carrier_status'],
                'edit_url' => get_admin_url(null, 'post.php?post=' . (int)$item['id'] . '&action=edit'),
                'status' => wc_get_order_status_name($item['status']),
                'state' => 'default',
                'shipping_method' => $item['shipping_method'] ? $item['shipping_method']->order_item_name : '',
                'errors' => [],
                'created_at' => date('d.m.Y H:i', strtotime($item['created_at']))
            ];

            foreach ($item['info'] as $info) {
                $order[ $this->mapKey($info['meta_key']) ] = $this->mapValue($info['meta_key'], $info['meta_value']);
            }

            if (!empty($order['label_id'])) {
                $order['label_downloads'] = [];
                foreach (WCUSHelper::getLabelDownloadFormats($order['label_carrier_slug'] ?? '') as $format => $name) {
                    $order['label_downloads'][] = [
                        'name' => $name,
                        'url' => admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $order['label_db_id'] . '&format=' . $format),
                    ];
                }
            }

            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param string $key
     * @return string
     */
    private function mapKey($key)
    {
        switch ($key) {
            case '_billing_first_name':
                return 'firstname';
            case '_billing_last_name':
                return 'lastname';
            case '_order_total':
                return 'total';
            default:
                return $key;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    private function mapValue($key, $value)
    {
        switch ($key) {
            case '_order_total':
                return wc_price($value);
            default:
                return $value;
        }
    }
}