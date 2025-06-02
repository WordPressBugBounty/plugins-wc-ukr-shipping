<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Api;

use kirillbdev\WCUkrShipping\Component\SmartyParcel\LabelRequestBuilderInterface;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUSCore\Http\Request;

final class SmartyParcelApi
{
    private const API_URL = 'https://api.smartyparcel.com';

    public function register(
        string $email,
        string $password,
        string $firstName,
        string $lastName
    ): string {
        $response = wp_remote_post(self::API_URL . '/register', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 5,
            'body' => json_encode([
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]),
        ]);
        $data = $this->processResponse($response);

        return $data['api_key'];
    }

    public function getUserStatus(string $apiKey): array
    {
        $response = wp_remote_get(self::API_URL . '/beta/user', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => $apiKey,
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 3,
        ]);

        return $this->processResponse($response);
    }

    public function connectApplication(string $apiKey): array
    {
        $response = wp_remote_post(self::API_URL . '/beta/applications/connect', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 5,
            'body' => json_encode([
                'api_key' => $apiKey,
                'site_url' => get_site_url(),
            ])
        ]);

        return $this->processResponse($response);
    }

    public function disconnectApplication(string $apiKey): array
    {
        $response = wp_remote_post(self::API_URL . '/beta/applications/disconnect', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => $apiKey,
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
        ]);

        return $this->processResponse($response);
    }

    public function getCarrierAccounts(string $apiKey): array
    {
        $response = wp_remote_get(self::API_URL . '/beta/carriers', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => $apiKey,
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
        ]);

        return $this->processResponse($response);
    }

    public function connectCarrier(
        string $carrierSlug,
        array $data
    ): array {
        if ($carrierSlug === 'nova_poshta') {
            $uri = '/beta/carriers/nova_poshta';
        } elseif ($carrierSlug === 'ukrposhta') {
            $uri = '/beta/carriers/ukrposhta';
        } else {
            throw new \InvalidArgumentException('Unknown carrier ' . sanitize_text_field($carrierSlug));
        }

        $response = wp_remote_post(self::API_URL . $uri, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode($data),
        ]);

        return $this->processResponse($response);
    }

    public function updateCarrier(
        string $id,
        string $accountApiKey,
        string $name,
        string $senderRef,
        string $senderContactRef
    ): array {
        $response = wp_remote_request(self::API_URL . '/beta/carriers/nova_poshta/' . $id, [
            'method' => 'PUT',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'name' => $name,
                'api_key' => $accountApiKey,
                'sender_ref' => $senderRef,
                'sender_contact_ref' => $senderContactRef,
            ])
        ]);

        return $this->processResponse($response);
    }

    public function createLabel(LabelRequestBuilderInterface $builder): array
    {
        $response = wp_remote_post(self::API_URL . '/beta/labels', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 15,
            'body' => json_encode($builder->build())
        ]);

        return $this->processResponse($response);
    }

    public function deleteCarrier(string $carrierId): array
    {
        $response = wp_remote_request(self::API_URL . "/beta/carriers/$carrierId", [
            'method' => 'DELETE',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
        ]);

        return $this->processResponse($response);
    }

    public function voidLabel(string $labelId): array
    {
        $response = wp_remote_request(self::API_URL . "/beta/labels/$labelId/void", [
            'method' => 'PUT',
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
        ]);

        return $this->processResponse($response);
    }

    public function estimateRates(
        string $carrierAccountId,
        string $shipFrom,
        string $shipTo,
        string $deliveryType,
        float $declaredValue,
        float $weight,
        ?string $serviceType = null
    ): array {
        $payload = [
            'carrier_account_id' => $carrierAccountId,
            'delivery_type' => $deliveryType,
            'ship_from' => [
                'country_code' => 'UA',
                'carrier_city_id' => $shipFrom,
            ],
            'ship_to' => [
                'country_code' => 'UA',
                'carrier_city_id' => $shipTo,
            ],
            'declared_value' => [
                'amount' => $declaredValue,
                'currency' => 'UAH',
            ],
            'weight' => [
                'value' => $weight,
                'unit' => 'kg',
            ]
        ];
        if ($serviceType !== null) {
            $payload['service_type'] = $serviceType;
        }

        $response = wp_remote_post(self::API_URL . "/beta/rates/estimate", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' => get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode($payload),
        ]);

        return $this->processResponse($response);
    }

    public function addTracking(string $trackingNumber, string $carrierSlug): array
    {
        $response = wp_remote_post(self::API_URL . "/beta/trackings", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'tracking_number' => $trackingNumber,
                'carrier_slug' => $carrierSlug,
            ]),
        ]);

        return $this->processResponse($response);
    }

    public function getTrackings(array $trackingNumbers): array
    {
        $response = wp_remote_post(self::API_URL . "/beta/trackings/search", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'tracking_numbers' => $trackingNumbers,
                'limit' => 100, // todo: hardcoded
            ]),
        ]);

        return $this->processResponse($response);
    }

    public function createSubscriptionChangeAction(string $subscription): array
    {
        $response = wp_remote_post(self::API_URL . "/beta/marketing/subscriptions/change", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
                'SP-Site-Url' => get_site_url(),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'subscription_slug' => $subscription,
            ]),
        ]);

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
