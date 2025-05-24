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
            'novaPoshtaShippingCost' => $this->getNovaPoshtaShippingCostState(),
            'ukrposhtaShippingCost' => $this->getUkrposhtaShippingCostState(),
            'ukrposhtaDeliveryData' => $this->getUkrposhtaDeliveryDataState(),
        ];
    }

    private function getNovaPoshtaShippingCostState(): array
    {
        $relativeCost = wc_ukr_shipping_get_option('wc_ukr_shipping_np_relative_price');
        $state = [
            'calc_type' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_price_type'),
            'fixed_price' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_price'),
            'cargo_type' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_cargo_type'),
            'total_cost' => $relativeCost
                ? json_decode($relativeCost, true)
                : [
                    [ 'total' => 0, 'price' => 50 ]
                ],
        ];

        return $state;
    }

    private function getUkrposhtaShippingCostState(): array
    {
        $relativeCost = wc_ukr_shipping_get_option('wcus_ukrposhta_relative_price');
        $ratesCity = json_decode(wc_ukr_shipping_get_option('wcus_ukrposhta_rates_city'), true);
        $state = [
            'calc_type' => wc_ukr_shipping_get_option('wcus_ukrposhta_price_type'),
            'fixed_price' => wc_ukr_shipping_get_option('wcus_ukrposhta_price'),
            'total_cost' => $relativeCost
                ? json_decode($relativeCost, true)
                : [
                    [ 'total' => 0, 'price' => 50 ]
                ],
            'rates_city' => is_array($ratesCity) ? $ratesCity : [
                'value' => '',
                'name' => '',
            ],
        ];

        return $state;
    }

    private function getUkrposhtaDeliveryDataState(): array
    {
        return [
            'provider' => wc_ukr_shipping_get_option('wcus_ukrposhta_dd_provider'),
            'bearerEcom' => wc_ukr_shipping_get_option('wcus_ukrposhta_bearer_ecom', ''),
        ];
    }
}
