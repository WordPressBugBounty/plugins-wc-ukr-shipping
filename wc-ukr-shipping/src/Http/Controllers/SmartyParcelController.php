<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

class SmartyParcelController extends Controller
{
    private SmartyParcelApi $api;
    private ShippingLabelsRepository $shippingLabelsRepository;

    public function __construct(
        SmartyParcelApi $api,
        ShippingLabelsRepository $shippingLabelsRepository
    ) {
        $this->api = $api;
        $this->shippingLabelsRepository = $shippingLabelsRepository;
    }

    public function connect(Request $request): ResponseInterface
    {
        try {
            $apiKey = $request->get('api_key');
            $data = $this->api->getUserStatus($apiKey);

            update_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY, $apiKey);
            if ($data['verified']) {
                update_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS, 'connected');
            } else {
                update_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS, 'waiting_verification');
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'auth_state' => get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function register(Request $request): ResponseInterface
    {
        try {
            $apiKey = $this->api->register(
                $request->get('email'),
                $request->get('password'),
                $request->get('first_name'),
                $request->get('last_name'),
            );
            update_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY, $apiKey);
            update_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS, 'waiting_verification');

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function checkVerification(Request $request): ResponseInterface
    {
        try {
            $data = $this->api->getUserStatus(get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY));
            if ($data['verified']) {
                update_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS, 'connected');
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'auth_state' => get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function disconnect(Request $request): ResponseInterface
    {
        try {
            delete_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
            delete_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS);
            delete_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS);

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function getCarrierAccounts(Request $request): ResponseInterface
    {
        try {
            $carrierAccounts = $this->getCarrierAccountCached();
            if (count($carrierAccounts) === 0) {
                $response = $this->api->getCarrierAccounts(
                    get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY)
                );
                $carrierAccounts = array_map(function ($acc) {
                    return [
                        'id' => $acc['id'],
                        'name' => $acc['name'],
                        'carrier_slug' => $acc['carrier_slug']
                    ];
                }, $response['carriers']);
                update_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS, json_encode($carrierAccounts));
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $carrierAccounts,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function connectCarrier(Request $request): ResponseInterface
    {
        try {
            $response = $this->api->connectCarrier(
                $request->get('api_key'),
                $request->get('name'),
                $request->get('sender_ref'),
                $request->get('sender_contact_ref')
            );
            $carrier = [
                'id' => $response['id'],
                'name' => $response['name'],
                'carrier_slug' => $response['carrier_slug'],
            ];

            $carrierAccounts = $this->getCarrierAccountCached();
            $carrierAccounts[] = $carrier;
            update_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS, json_encode($carrierAccounts));

            return $this->jsonResponse([
                'success' => true,
                'data' => $carrier,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function deleteCarrierAccount(Request $request): ResponseInterface
    {
        try {
            $this->api->deleteCarrier($request->get('id'));
            $carrierAccounts = $this->getCarrierAccountCached();
            $newAccounts = [];
            foreach ($carrierAccounts as $acc) {
                if ($acc['id'] !== $request->get('id')) {
                    $newAccounts[] = $acc;
                }
            }
            update_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS, json_encode($newAccounts));

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function createShippingLabel(Request $request): ResponseInterface
    {
        try {
            // todo: provide dto
            $response = $this->api->createLabel($request);
            $this->shippingLabelsRepository->create(
                (int)$request->get('ttn')['order_id'],
                $response['id'],
                $response['tracking_number'],
                $response['carrier_slug'] ?? 'nova_poshta'
            );
            $shippingLabel = $this->shippingLabelsRepository->findByOrderId((int)$request->get('ttn')['order_id']);

            $downloads = [];
            foreach (['a4', 'm85', 'm100'] as $format) {
                $downloads[] = [
                    'format' => $format,
                    'url' => admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $shippingLabel['id'] . '&format=' . $format),
                ];
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $response['id'],
                    'tracking_number' => $response['tracking_number'],
                    'shipment_cost' => $response['shipment_cost']['amount'],
                    'estimated_delivery_date' => $response['estimated_delivery_date'],
                    'order_url' => get_admin_url( null, 'post.php?post=' . (int)$request->get('ttn')['order_id'] . '&action=edit'),
                    'downloads' => $downloads,
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function voidLabel(Request $request): ResponseInterface
    {
        try {
            $label = $this->shippingLabelsRepository->findById((int)$request->get('label_id'));
            if ($label === null) {
                throw new \Exception('Label by id ' . $request->get('label_id') . ' not found');
            }
            $response = $this->api->voidLabel($label['label_id']);
            $this->shippingLabelsRepository->deleteById((int)$label['id']);

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function getCarrierAccountCached(): array
    {
        $carrierAccounts = get_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS);
        if ($carrierAccounts) {
            $carrierAccounts = json_decode($carrierAccounts, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $carrierAccounts;
            }
        }

        return [];
    }
}
