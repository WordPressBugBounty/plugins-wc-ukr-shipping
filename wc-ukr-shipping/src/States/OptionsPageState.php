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
        ];
    }
}
