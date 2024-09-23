<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Address\Model\City;
use kirillbdev\WCUkrShipping\Address\Model\Warehouse;
use kirillbdev\WCUkrShipping\Address\Provider\AddressProviderInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

if ( ! defined('ABSPATH')) {
    exit;
}

class AddressController extends Controller
{
    private AddressProviderInterface $addressProvider;

    public function __construct(AddressProviderInterface $addressProvider)
    {
        $this->addressProvider = $addressProvider;
    }

    public function searchCities(Request $request)
    {
        if (  ! $request->get('query')) {
            return $this->jsonResponse([
                'success' => true,
                'data' => []
            ]);
        }

        return $this->jsonResponse([
            'success' => true,
            'data' => $this->mapCities(
                $this->addressProvider->searchCitiesByQuery($request->get('query')),
                $request->get('lang', '')
            )
        ]);
    }

    public function searchWarehouses(Request $request)
    {
        if ( ! $request->get('city_ref') || ! (int)$request->get('page')) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $types = [
            WCUS_WAREHOUSE_TYPE_REGULAR,
            WCUS_WAREHOUSE_TYPE_CARGO,
        ];
        $result = $this->addressProvider->searchWarehousesByQuery(
            $request->get('city_ref'),
            $request->get('query', ''),
            (int)$request->get('page'),
            $types
        );

        if (!count($result->getWarehouses())) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $items = $this->mapWarehouses(
            $result->getWarehouses(),
            $request->get('lang', '')
        );

        $offset = ((int)$request->get('page') - 1) * 20 + count($items);

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'items' => $items,
                'more' => $offset < $result->getTotal(),
            ]
        ]);
    }

    public function searchPoshtomats(Request $request)
    {
        if ( ! $request->get('city_ref') || ! (int)$request->get('page')) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $result = $this->addressProvider->searchWarehousesByQuery(
            $request->get('city_ref'),
            $request->get('query', ''),
            (int)$request->get('page'),
            [
                WCUS_WAREHOUSE_TYPE_POSHTOMAT,
            ]
        );

        if (!count($result->getWarehouses())) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $items = $this->mapWarehouses(
            $result->getWarehouses(),
            $request->get('lang', '')
        );

        $offset = ((int)$request->get('page') - 1) * 20 + count($items);

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'items' => $items,
                'more' => $offset < $result->getTotal(),
            ]
        ]);
    }

    /**
     * @param City[] $cities
     * @param $locale
     * @return array[]
     */
    private function mapCities(array $cities, string $locale): array
    {
        return array_map(function (City $item) use ($locale) {
            return [
                'value' => $item->getRef(),
                'name' => $locale === 'ru' ? $item->getNameRu() : $item->getNameUa(),
            ];
        }, $cities);
    }

    /**
     * @param Warehouse $warehouses
     * @param string $locale
     * @return array
     */
    private function mapWarehouses(array $warehouses, string $locale): array
    {
        return array_map(function (Warehouse $item) use ($locale) {
            return [
                'value' => $item->getRef(),
                'name' => $locale === 'ru' ? $item->getNameRu() : $item->getNameUa(),
            ];
        }, $warehouses);
    }
}
