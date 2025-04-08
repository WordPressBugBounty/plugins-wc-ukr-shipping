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
        $shipTo = [
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
        ];
        if ($request->get('recipient')['service_type'] === 'Warehouse') {
            $shipTo['carrier_city_id'] = $request->get('recipient')['city_ref'];
            $shipTo['carrier_warehouse_id'] = $request->get('recipient')['warehouse_ref'];
        } else {
            $shipTo['country_code'] = 'UA';
            $shipTo['city'] = $request->get('recipient')['settlement_name'];
            $shipTo['state'] = $request->get('recipient')['settlement_area'];
            $shipTo['district'] = $request->get('recipient')['settlement_region'];
            $shipTo['address_1'] = $request->get('recipient')['street_name'];
            $shipTo['address_2'] = $request->get('recipient')['house'];
            $shipTo['address_3'] = $request->get('recipient')['flat'];
        }

        $shipFrom = [];
        if ($request->get('sender')['service_type'] === 'Warehouse') {
            $shipFrom['carrier_city_id'] = $request->get('sender')['city_ref'];
            $shipFrom['carrier_warehouse_id'] = $request->get('sender')['warehouse_ref'];
        } else {
            $shipFrom['country_code'] = 'UA';
            $shipFrom['city'] = $request->get('sender')['settlement_name'];
            $shipFrom['state'] = $request->get('sender')['settlement_area'];
            $shipFrom['district'] = $request->get('sender')['settlement_region'];
            $shipFrom['address_1'] = $request->get('sender')['street_name'];
            $shipFrom['address_2'] = $request->get('sender')['house'];
            $shipFrom['address_3'] = $request->get('sender')['flat'];
        }

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
                'ship_from' => $shipFrom,
                'ship_to' => $shipTo,
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

    public function estimateRates(
        string $carrierAccountId,
        string $shipFrom,
        string $shipTo,
        string $deliveryType,
        float $declaredValue,
        float $weight
    ): array {
        $response = wp_remote_post(self::API_URL . "/beta/rates/estimate", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
            ],
            'timeout' => 5,
            'body' => json_encode([
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
            ])
        ]);

        return $this->processResponse($response);
    }

    public function addTracking(string $trackingNumber): array
    {
        $response = wp_remote_post(self::API_URL . "/beta/trackings", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
            ],
            'timeout' => 5,
            'body' => json_encode([
                'tracking_number' => $trackingNumber,
                'carrier_slug' => 'nova_poshta',
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
            ],
            'timeout' => 5,
            'body' => json_encode([
                'tracking_numbers' => $trackingNumbers,
                'limit' => 100, // todo: hardcoded
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
