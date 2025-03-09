<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Api;

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
            ],
            'timeout' => 5,
        ]);

        return $this->processResponse($response);
    }

    public function connectCarrier(
        string $accountApiKey,
        string $name,
        string $senderRef,
        string $senderContactRef
    ): array {
        $response = wp_remote_post(self::API_URL . '/beta/carriers/nova_poshta', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'api_key' => $accountApiKey,
                'sender_ref' => $senderRef,
                'sender_contact_ref' => $senderContactRef,
                'name' => $name,
            ])
        ]);

        return $this->processResponse($response);
    }

    public function createLabel(Request $request): array
    {
        $labelRequest = [
            'carrier_account_id' => $request->get('sender')['carrier_account_id'],
            'billing' => [
                'paid_by' => strtolower($request->get('ttn')['payer_type']),
                'payment_method' => $request->get('ttn')['payment_method'] === 'NonCash'
                    ? 'card'
                    : 'cash',
            ],
            'shipment' => [
                'ship_date' => $request->get('ttn')['date'],
                'ship_from' => [
                    'carrier_city_id' => $request->get('sender')['city_ref'],
                    'carrier_warehouse_id' => $request->get('sender')['warehouse_ref'],
                ],
                'ship_to' => [
                    'name' => sprintf(
                        '%s %s%s',
                        $request->get('recipient')['firstname'],
                        $request->get('recipient')['lastname'],
                        $request->get('recipient')['middlename']
                            ? ' ' . $request->get('recipient')['middlename']
                            : '',
                    ),
                    'phone' => WCUSHelper::preparePhone($request->get('recipient')['phone']),
                    'email' => $request->get('recipient')['email'] ?? null,
                    'carrier_city_id' => $request->get('recipient')['city_ref'],
                    'carrier_warehouse_id' => $request->get('recipient')['warehouse_ref'],
                ],
            ]
        ];

        // Parcels
        $parcels = [];
        foreach ($request->get('ttn')['seats'] as $index => $seat) {
            $parcels[] = [
                'insurance_cost' => $index === 0 ? $request->get('ttn')['cost'] : 0,
                'weight' => [
                    'value' => (float)$seat['weight'],
                    'unit' => 'kg',
                ],
                'dimensions' => [
                    'width' => (int)$seat['width'],
                    'height' => (int)$seat['height'],
                    'length' => (int)$seat['length'],
                    'unit' => 'cm',
                ],
                'description' => $index === 0 ? $request->get('ttn')['description'] : '-',
            ];
        }
        $labelRequest['shipment']['parcels'] = $parcels;

        if (!empty($request->get('ttn')['barcode'])) {
            $labelRequest['shipment']['external_order_id'] =  $request->get('ttn')['barcode'];
        }

        // Payment Control and COD
        if ($request->get('ttn')['payment_control'] === '1') {
            $labelRequest['service_options']['cod'] = [
                'payment_method' => 'cash_equivalent',
                'value' => [
                    'amount' => (float)$request->get('ttn')['payment_control_cost'],
                    'currency' => 'UAH',
                ],
            ];
        } elseif ($request->get('ttn')['backward_delivery'] === '1') {
            $labelRequest['service_options']['cod'] = [
                'payment_method' => 'cash',
                'value' => [
                    'amount' => (float)$request->get('ttn')['backward_delivery_cost'],
                    'currency' => 'UAH',
                ],
                'options' => [
                    'nova_poshta_cod_payer' => $request->get('ttn')['backward_delivery_payer'] === 'Sender'
                        ? 'sender'
                        : 'recipient',
                ]
            ];
        }

        $response = wp_remote_post(self::API_URL . '/beta/labels', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
            ],
            'timeout' => 10,
            'body' => json_encode($labelRequest)
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
            ],
            'timeout' => 5,
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
