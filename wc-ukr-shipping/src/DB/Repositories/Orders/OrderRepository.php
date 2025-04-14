<?php

namespace kirillbdev\WCUkrShipping\DB\Repositories\Orders;

use kirillbdev\WCUSCore\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function getOrdersWithTTN(int $offset, int $limit): array
    {
        return DB::table(DB::posts() . ' as p')
            ->leftJoin(DB::prefixedTable('wc_ukr_shipping_labels') . ' as l', 'p.ID = l.order_id')
            ->where('p.post_type', 'shop_order')
            ->where('p.post_status', '!=', 'trash')
            ->orderBy('p.ID', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get([
                'p.ID as id',
                'p.post_date as created_at',
                'p.post_status as status',
                'l.label_id',
                'l.id as label_db_id',
                'l.tracking_number',
                'l.tracking_status',
                'l.carrier_status_code',
                'l.carrier_status',
            ]);
    }

    public function getOrderInfo(int $orderId): array
    {
        return DB::table(DB::postmeta())
            ->where('post_id', (int)$orderId)
            ->whereIn('meta_key', [
                '_billing_last_name',
                '_billing_first_name',
                '_order_total'
            ])
            ->get([
                'meta_key',
                'meta_value'
            ]);
    }

    public function getOrderShippingMethod(int $orderId): ?\stdClass
    {
        return DB::table(DB::woocommerceOrderItems())
            ->where('order_id', (int)$orderId)
            ->where('order_item_type', 'shipping')
            ->first([
                'order_item_name'
            ]);
    }

    public function getCountOrderPages(int $limit): int
    {
        $pageCount = DB::table(DB::posts() . ' as p')
            ->where('p.post_type', 'shop_order')
            ->where('p.post_status', '!=', 'trash')
            ->count('p.ID');

        return ceil($pageCount / $limit);
    }
}