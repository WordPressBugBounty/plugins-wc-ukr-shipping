<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Api;

use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\StoreNotConnectedException;
use kirillbdev\WCUkrShipping\Helpers\SmartyParcelHelper;

final class SmartyParcelWPApi
{
    private const API_URL = 'https://wp-api.smartyparcel.com';
    private const APP_NAME = 'wc-ukr-shipping';

    private const ROUTE_METHOD_MAP = [
        '/v1/account' => 'GET',
        '/v1/dashboard/overview' => 'GET',
        '/v1/carriers/list' => 'GET',
        '/v1/carriers' => 'GET',
        '/v1/billing/plans' => 'GET',
        '/v1/locator/cities' => 'GET',
        '/v1/locator/pudo-points' => 'GET',
        '/v1/batches' => 'POST',
        '/v1/batches/:id' => 'GET',
        '/v1/downloads/l5/:uuid.pdf' => 'GET',
        '/v1/downloads/b6/:uuid.pdf' => 'GET',
        '/v1/embedded/authx' => 'POST',
        '/v1/lookup/company' => 'GET',
        '/v1/rates/estimate' => 'POST',
        '/v1/addresses' => 'GET',
        '/v1/orders' => 'POST',
        '/v1/orders/label' => 'POST',
        '/v1/checkout/link' => 'POST',

        // Label
        '/v1/labels' => 'POST',
        '/v1/labels/prepare' => 'POST',

        // Legacy route for migration settings
        '/v1/legacy/settings/migrate' => 'POST',

        // Public routes
    ];

    private const PUBLIC_ROUTES = [
    ];

    public function connectApplication(string $accessToken): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        // The platform binds the guest key to the store and revokes it
        if (SmartyParcelHelper::isGuest()) {
            $headers['SP-API-Key'] = get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
        }

        $response = wp_remote_post(self::API_URL . '/v1/app/connect', [
            'headers' => $headers,
            'timeout' => 5,
            'body' => json_encode([
                'store_url' => SmartyParcelHelper::getStoreUrl(),
                'app_name' => self::APP_NAME,
            ])
        ]);

        return $this->processResponse($response);
    }

    /**
     * Issues a locator-only key without an account. Must be called only after
     * the store owner accepted the consent text.
     *
     * @return array{guest_id: string, key: string}
     */
    public function guestConnect(string $consentVersion): array
    {
        $payload = [
            'store_url' => SmartyParcelHelper::getStoreUrl(),
            'app_name' => self::APP_NAME,
            'consent_version' => $consentVersion,
        ];
        if (wcus_is_woocommerce_active()) {
            $payload['country'] = WC()->countries->get_base_country();
            $payload['currency'] = get_woocommerce_currency();
        }

        $response = wp_remote_post(self::API_URL . '/v1/app/guest-connect', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
            'body' => json_encode($payload),
        ]);

        return $this->processResponse($response);
    }

    public function assistantConnect(string $consentVersion): array
    {
        $response = wp_remote_post(WCUS_SMARTY_PARCEL_ASSISTANT_API_URL . '/connect', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10,
            'body' => json_encode([
                'platform' => 'woocommerce',
                'site_url' => SmartyParcelHelper::getStoreUrl(),
                'app_name' => self::APP_NAME,
                'consent_version' => $consentVersion,
            ]),
        ]);

        return $this->processResponse($response)['data'] ?? [];
    }

    public function assistantToken(string $key): array
    {
        $response = wp_remote_post(WCUS_SMARTY_PARCEL_ASSISTANT_API_URL . '/token', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $key,
            ],
            'timeout' => 10,
        ]);

        return $this->processResponse($response)['data'] ?? [];
    }

    public function sendRequest(
        string $route,
        ?array $payload = null,
        array $query = [],
        array $pathParameters = []
    ): array {
        if ( ! isset(self::ROUTE_METHOD_MAP[$route])) {
            throw new \Exception('Route not found: ' . $route);
        }

        $this->mustCheckConnection($route);

        $method = self::ROUTE_METHOD_MAP[$route];
        $args = [
            'method' => $method,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => SmartyParcelHelper::getStoreUrl(),
            ],
            'timeout' => 10,
        ];
        if ($payload !== null && $method === 'POST') {
            $args['body'] = json_encode($payload);
        }

        // Try to replace path placeholders
        foreach ($pathParameters as $name => $value) {
            $route = str_replace(":$name", $value, $route);
        }

        $url = self::API_URL . $route;
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }

        return $this->processResponse(wp_remote_request($url, $args));
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

        if (in_array($code, [200, 201])) {
            return $payload;
        }

        throw new SmartyParcelErrorException(
            $payload['error']['code'] ?? 0,
            $payload['error']['message'] ?? 'Unknown error',
            $payload['error']['details'] ?? []
        );
    }

    private function mustCheckConnection(string $route): void
    {
        if ( !in_array($route, self::PUBLIC_ROUTES) && !get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY)) {
            throw new StoreNotConnectedException();
        }
    }
}
