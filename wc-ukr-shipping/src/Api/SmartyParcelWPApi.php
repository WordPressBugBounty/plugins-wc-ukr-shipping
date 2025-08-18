<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Api;

use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;

final class SmartyParcelWPApi
{
    private const API_URL = 'https://wp-api.smartyparcel.com';

    private const ROUTE_METHOD_MAP = [
        '/v1/account' => 'GET',
        '/v1/dashboard/overview' => 'GET',
        '/v1/carriers' => 'GET',
        '/v1/billing/plans' => 'GET',
    ];

    public function connectApplication(string $accessToken): array
    {
        $response = wp_remote_post(self::API_URL . '/v1/app/connect', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'timeout' => 5,
            'body' => json_encode([
                'store_url' => get_site_url(),
            ])
        ]);

        return $this->processResponse($response);
    }

    public function sendRequest(string $route, ?array $payload = null): array {
        if ( ! get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY)) {
            throw new \Exception('Store not connected to SmartyParcel');
        }

        if ( ! isset(self::ROUTE_METHOD_MAP[$route])) {
            throw new \Exception('Route not found: ' . $route);
        }

        $args = [
            'method' => self::ROUTE_METHOD_MAP[$route],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
        ];
        if ($payload !== null) {
            $args['body'] = json_encode($payload);
        }

        $response = wp_remote_request(self::API_URL . $route, $args);

        return $this->processResponse($response);
    }

    private function processResponse($response): array
    {
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $code = (int)wp_remote_retrieve_response_code($response);
        if (empty($response['body'])) {
            $payload = [];
        } else {
            $result = json_decode($response['body'], true);
            if (json_last_error()) {
                throw new \Exception("API error: malformed response");
            }
            $payload = $result;
        }

        if ($code === 200) {
            return $payload;
        }

        throw new SmartyParcelErrorException(
            $payload['error']['code'] ?? 0,
            $payload['error']['message'] ?? 'Unknown error',
            $payload['error']['details'] ?? []
        );
    }
}
