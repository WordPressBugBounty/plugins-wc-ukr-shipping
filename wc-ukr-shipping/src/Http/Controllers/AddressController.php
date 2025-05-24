<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Address\Provider\AddressProviderInterface;
use kirillbdev\WCUkrShipping\Component\Shipping\NovaPoshtaPUDOProvider;
use kirillbdev\WCUkrShipping\Component\Shipping\UkrposhtaPUDOProvider;
use kirillbdev\WCUkrShipping\Contracts\Shipping\PUDOProviderInterface;
use kirillbdev\WCUkrShipping\Dto\Shipping\City;
use kirillbdev\WCUkrShipping\Dto\Shipping\PUDO;
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
        $provider = $this->getPUDOProvider($request->get('carrier'));

        return $this->jsonResponse([
            'success' => true,
            'data' => $this->mapCities(
                $provider->searchCitiesByQuery($request->get('query')),
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

        $provider = $this->getPUDOProvider($request->get('carrier'));
        try {
            $result = $provider->searchPUDOByQuery(
                $request->get('city_ref'),
                $request->get('query', ''),
                (int)$request->get('page'),
                $request->get('types', [
                    PUDO::PUDO_TYPE_WAREHOUSE,
                    PUDO::PUDO_TYPE_LOCKER,
                ])
            );
        } catch (\Throwable $e) {
        }

        if (count($result['data']) === 0) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $items = $this->mapWarehouses($result['data'], $request->get('lang', ''));
        $offset = ((int)$request->get('page') - 1) * 20 + count($items);

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'items' => $items,
                'more' => $offset < $result['total'],
            ]
        ]);
    }

    public function adminSearchWarehouses(Request $request)
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
            WCUS_WAREHOUSE_TYPE_POSHTOMAT,
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

        $provider = $this->getPUDOProvider($request->get('carrier'));
        $result = $provider->searchPUDOByQuery(
            $request->get('city_ref'),
            $request->get('query', ''),
            (int)$request->get('page'),
            [
                PUDO::PUDO_TYPE_LOCKER,
            ]
        );

        if (count($result['data']) === 0) {
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => [],
                    'more' => false
                ]
            ]);
        }

        $items = $this->mapWarehouses($result['data'], $request->get('lang', ''));
        $offset = ((int)$request->get('page') - 1) * 20 + count($items);

        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'items' => $items,
                'more' => $offset < $result['total'],
            ]
        ]);
    }

    /**
     * @param City[] $cities
     * @param string $locale
     * @return array
     */
    private function mapCities(array $cities, string $locale): array
    {
        return array_map(function (City $item) use ($locale) {
            return [
                'value' => $item->id,
                'name' => $locale === 'ru' ? $item->nameRu : $item->nameUa,
            ];
        }, $cities);
    }

    /**
     * @param PUDO[] $warehouses
     * @param string $locale
     * @return array
     */
    private function mapWarehouses(array $warehouses, string $locale): array
    {
        return array_map(function (PUDO $item) use ($locale) {
            return [
                'value' => $item->id,
                'name' => $locale === 'ru' ? $item->nameRu : $item->nameUa,
            ];
        }, $warehouses);
    }

    private function getPUDOProvider(string $carrier): PUDOProviderInterface
    {
        switch ($carrier) {
            case 'nova_poshta':
                return wcus_container()->make(NovaPoshtaPUDOProvider::class);
            case 'ukrposhta':
                return wcus_container()->make(UkrposhtaPUDOProvider::class);
        }

        throw new \Exception('Wrong');
    }
}
