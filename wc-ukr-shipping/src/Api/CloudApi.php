<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Api;

use kirillbdev\WCUkrShipping\Contracts\NovaPoshtaAddressProviderInterface;
use kirillbdev\WCUkrShipping\Exceptions\NovaPoshtaAddressProviderException;
use kirillbdev\WCUkrShipping\Model\NovaPoshta\Area;
use kirillbdev\WCUkrShipping\Model\NovaPoshta\City;
use kirillbdev\WCUkrShipping\Model\NovaPoshta\Warehouse;

final class CloudApi implements NovaPoshtaAddressProviderInterface
{
    private string $cloudUrl = 'https://api.wcukraineshipping.com';

    public function getAreas(): array
    {
        try {
            $response = $this->sendRequest('/v1/addresses/loadAreas', []);

            return array_map(function (array $data) {
                return new Area($data['ref'], $data['nameRu'], $data['nameUa']);
            }, $response['areas']);
        } catch (\Exception $e) {
            throw new NovaPoshtaAddressProviderException($e->getMessage());
        }
    }

    public function getCities(int $page, int $limit): array
    {
        try {
            $response = $this->sendRequest('/v1/addresses/loadCities', [
                'page' => $page,
                'limit' => $limit,
            ]);

            return array_map(function (array $data) {
                return new City($data['ref'], $data['areaRef'], $data['nameRu'], $data['nameUa']);
            }, $response['cities']);
        } catch (\Exception $e) {
            throw new NovaPoshtaAddressProviderException($e->getMessage());
        }
    }

    public function getWarehouses(int $page, int $limit): array
    {
        try {
            $response = $this->sendRequest('/v1/addresses/loadWarehouses', [
                'page' => $page,
                'limit' => $limit,
            ]);

            return array_map(function (array $data) {
                return new Warehouse(
                    $data['ref'],
                    $data['cityRef'],
                    $data['nameRu'],
                    $data['nameUa'],
                    (int)$data['number'],
                    $this->mapWarehouseType($data['warehouseType'])
                );
            }, $response['warehouses']);
        } catch (\Exception $e) {
            throw new NovaPoshtaAddressProviderException($e->getMessage());
        }
    }

    private function mapWarehouseType(string $responseType): int
    {
        switch ($responseType) {
            case 'cargo':
                return Warehouse::TYPE_CARGO;
            case 'poshtomat':
                return Warehouse::TYPE_POSHTOMAT;
            default:
                return Warehouse::TYPE_REGULAR;
        }
    }

    /**
     * @throws \Exception
     */
    private function sendRequest(string $endpoint, array $payload = [])
    {
        $response = wp_remote_post($this->cloudUrl . $endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15, // Hardcoded at now
            'body' => json_encode($payload),
        ]);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $code = (int)wp_remote_retrieve_response_code($response);

        if ($code !== 200) {
            throw new \Exception("External API error: Bad request or communication error");
        }

        $result = json_decode($response['body'], true);
        if (json_last_error()) {
            throw new \Exception("External API error: malformed response");
        }

        return $result;
    }
}
