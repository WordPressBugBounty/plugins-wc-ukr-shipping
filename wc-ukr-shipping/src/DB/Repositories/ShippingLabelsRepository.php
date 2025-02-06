<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories;

use kirillbdev\WCUSCore\Facades\DB;

class ShippingLabelsRepository
{
    public function findByOrderId(int $orderId): ?array
    {
        $result = DB::table(DB::prefixedTable('wc_ukr_shipping_labels'))
            ->where('order_id', $orderId)
            ->first();

        return $result !== null ? (array)$result : null;
    }

    public function findById(int $id): ?array
    {
        $result = DB::table(DB::prefixedTable('wc_ukr_shipping_labels'))
            ->where('id', $id)
            ->first();

        return $result !== null ? (array)$result : null;
    }

    public function deleteById(int $id): void
    {
        global $wpdb;
        $wpdb->delete(DB::prefixedTable('wc_ukr_shipping_labels'), [
            'id' => $id
        ], [
            'id' => '%d'
        ]);
    }

    public function create(
        int $orderId,
        string $labelId,
        string $trackingNumber,
        string $carrierSlug
    ) {
        $now = date('Y-m-d H:i:s');
        DB::table(DB::prefixedTable('wc_ukr_shipping_labels'))
            ->insert([
                'label_id' => $labelId,
                'carrier_slug' => $carrierSlug,
                'order_id' => $orderId,
                'tracking_number' => $trackingNumber,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
    }
}
