<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Shipping;

use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Contracts\Shipping\GeoPUDOProviderInterface;
use kirillbdev\WCUkrShipping\Contracts\Shipping\PUDOProviderInterface;
use kirillbdev\WCUkrShipping\Dto\Shipping\City;
use kirillbdev\WCUkrShipping\Dto\Shipping\PUDO;
use kirillbdev\WCUkrShipping\Dto\Shipping\SearchPUDOByGeoRequestDTO;
use kirillbdev\WCUkrShipping\Dto\Shipping\SearchPUDORequestDTO;
use kirillbdev\WCUkrShipping\Enums\CarrierSlug;

class SmartyParcelPUDOProvider implements PUDOProviderInterface, GeoPUDOProviderInterface
{
    /**
     * Platform-wide maximum of points per response.
     */
    private const POINTS_LIMIT = 20;

    /**
     * Geo search is served with a wider limit - a map viewport holds far more
     * points than a paged list does.
     */
    private const GEO_POINTS_LIMIT = 50;

    private const INTL_CARRIERS = [
        CarrierSlug::NOVA_POST,
        CarrierSlug::POST_NORD,
        CarrierSlug::INPOST,
        CarrierSlug::GLS,
    ];

    private string $carrierSlug;
    private string $lang;
    private SmartyParcelWPApi $api;

    public function __construct(string $carrierSlug, string $lang, SmartyParcelWPApi $api)
    {
        $this->carrierSlug = $carrierSlug;
        $this->lang = $lang;
        $this->api = $api;
    }

    public function searchCitiesByQuery(string $query): array
    {
        $response = $this->api->sendRequest('/v1/locator/cities', null, [
            'carrier_slug' => $this->carrierSlug,
            'query' => $query,
            'language' => $this->lang,
        ]);

        return array_map(function (array $city) {
            return new City($city['carrier_city_id'], $city['name'], $city['name']);
        }, $response['cities']);
    }

    public function searchCityById(string $id): ?City
    {
        throw new \RuntimeException('Not implemented');
    }

    public function searchPUDOByQuery(SearchPUDORequestDTO $request): array
    {
        $params = [
            'carrier_slug' => $this->carrierSlug,
            'language' => $this->lang,
            'page' => $request->page,
            'limit' => self::POINTS_LIMIT,
            'types' => $this->mapTypes($request->types),
        ];
        if (!empty($request->query)) {
            $params['query'] = $request->query;
        }
        if (isset($request->weight) && $request->weight > 0) {
            $params['weight'] = [
                'value' => (float)$request->weight,
                'unit' => 'kg',
            ];
        }

        if (in_array($this->carrierSlug, self::INTL_CARRIERS, true)) {
            $params['country_code'] = $request->cityId;
        } else {
            $params['carrier_city_id'] = $request->cityId;
        }
        $response = $this->api->sendRequest('/v1/locator/pudo-points', null, $params);

        $data = array_map(function (array $item) use ($request) {
            return $this->mapPUDO($item, $request->cityId);
        }, $response['pudo_points']);

        return [
            'data' => $data,
            'total' => count($data),
        ];
    }

    public function searchPUDOByGeo(SearchPUDOByGeoRequestDTO $request): array
    {
        $params = [
            'carrier_slug' => $this->carrierSlug,
            'language' => $this->lang,
            'limit' => self::GEO_POINTS_LIMIT,
            'types' => $this->mapTypes($request->types),
            'country_code' => $request->countryCode,
            'lat' => $request->lat,
            'lng' => $request->lng,
            // The locator expects meters, the plugin counts in kilometers
            'radius' => (int)round($request->radius * 1000),
        ];
        if (isset($request->weight) && $request->weight > 0) {
            $params['weight'] = [
                'value' => (float)$request->weight,
                'unit' => 'kg',
            ];
        }

        $response = $this->api->sendRequest('/v1/locator/pudo-points', null, $params);

        return array_map(function (array $item) {
            // Carrier specific data of the point. Adapters that still address a
            // pudo point by its city hand the city ref over here - Ukrposhta
            // answers geo lookups in its own shape and never does
            $extra = $item['extra'] ?? [];

            return $this->mapPUDO($item, (string)($extra['carrier_city_id'] ?? ''));
        }, $response['pudo_points']);
    }

    public function searchPUDOById(string $id): ?PUDO
    {
        throw new \RuntimeException('Not implemented');
    }

    private function mapTypes(array $types): array
    {
        $mappedTypes = [];
        if (in_array(PUDO::PUDO_TYPE_WAREHOUSE, $types, true)) {
            $mappedTypes[] = 'warehouse';
            $mappedTypes[] = 'pudo';
        }
        if (in_array(PUDO::PUDO_TYPE_LOCKER, $types, true)) {
            $mappedTypes[] = 'parcel_locker';
        }

        return $mappedTypes;
    }

    private function mapPUDO(array $item, string $cityId): PUDO
    {
        return new PUDO(
            $item['carrier_pudo_id'],
            $cityId,
            $item['name'],
            $item['name'],
            $item['type'] === 'parcel_locker'
                ? PUDO::PUDO_TYPE_LOCKER
                : PUDO::PUDO_TYPE_WAREHOUSE,
            [
                'country_code' => $item['country_code'],
                'city' => $item['city'],
                'address_1' => $item['address_1'],
                'postal_code' => $item['postal_code'],
                'lat' => $item['lat'],
                'lng' => $item['lng'],
            ]
        );
    }
}
