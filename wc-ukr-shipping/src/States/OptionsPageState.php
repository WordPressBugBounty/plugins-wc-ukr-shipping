<?php

namespace kirillbdev\WCUkrShipping\States;

use kirillbdev\WCUkrShipping\Includes\Address\RepositoryCityFinder;
use kirillbdev\WCUkrShipping\Includes\Address\RepositoryWarehouseFinder;
use kirillbdev\WCUkrShipping\Includes\AppState;
use kirillbdev\WCUkrShipping\Includes\UI\CityUIValue;
use kirillbdev\WCUkrShipping\Includes\UI\WarehouseUIValue;

class OptionsPageState extends AppState
{
    protected function getState(): array
    {
        $cityFinder = new RepositoryCityFinder(
            wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_city')
        );
        $warehouseFinder = new RepositoryWarehouseFinder(
            wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_warehouse')
        );

        return [
            'novaPoshtaTtn' => [
                'senderCity' => CityUIValue::fromFinder($cityFinder),
                'senderWarehouse' => WarehouseUIValue::fromFinder($warehouseFinder),
            ],
            'shippingCost' => $this->getShippingCostState(),
        ];
    }

    private function getShippingCostState(): array
    {
        $totalCost = wc_ukr_shipping_get_option('wc_ukr_shipping_np_relative_price');

        $state = [
            'calc_type' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_price_type'),
            'fixed_price' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_price'),
            'cargo_type' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_cargo_type'),
            'total_cost' => $totalCost
                ? json_decode($totalCost, true)
                : [
                    [ 'total' => 0, 'price' => 50 ]
                ],
        ];

        return $state;
    }
}
