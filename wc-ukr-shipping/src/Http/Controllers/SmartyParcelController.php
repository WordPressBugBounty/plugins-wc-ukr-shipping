<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\FormLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\OrderLabelRequestBuilder;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Services\AutomationService;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

class SmartyParcelController extends Controller
{
    private SmartyParcelApi $api;
    private SmartyParcelService $smartyParcelService;
    private ShippingLabelsRepository $shippingLabelsRepository;
    private AutomationService $automationService;

    public function __construct(
        SmartyParcelApi $api,
        SmartyParcelService $smartyParcelService,
        ShippingLabelsRepository $shippingLabelsRepository,
        AutomationService $automationService
    ) {
        $this->api = $api;
        $this->smartyParcelService = $smartyParcelService;
        $this->shippingLabelsRepository = $shippingLabelsRepository;
        $this->automationService = $automationService;
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
                    'account' => $data,
                ]
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
                    'account' => $data,
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function refreshAccountInfo(Request $request): ResponseInterface
    {
        try {
            $account = $this->api->getUserStatus(get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY));
            set_transient('smarty_parcel_account', $account, 900);

            return $this->jsonResponse([
                'success' => true,
                'data' => $account,
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
            delete_option('wcus_nova_poshta_default_carrier');
            delete_transient('smarty_parcel_account');

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
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => 0,
                    'message' => $e->getMessage(),
                ],
            ]);
        }
    }

    public function updateCarrier(Request $request): ResponseInterface
    {
        try {
            $response = $this->api->updateCarrier(
                $request->get('carrier_account_id'),
                $request->get('api_key'),
                $request->get('name'),
                $request->get('sender_ref'),
                $request->get('sender_contact_ref')
            );

            $carrierAccounts = $this->getCarrierAccountCached();
            foreach ($carrierAccounts as &$acc) {
                if ($acc['id'] === $request->get('carrier_account_id')) {
                    $acc['name'] = $response['name'];
                }
            }
            update_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS, json_encode($carrierAccounts));

            return $this->jsonResponse([
                'success' => true,
                'data' => $carrierAccounts,
            ]);
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => 0,
                    'message' => $e->getMessage(),
                ],
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
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => '[' . $e->getCode() . '] ' . $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function createShippingLabel(Request $request): ResponseInterface
    {
        try {
            $response = $this->smartyParcelService->createLabel(
                (int)$request->get('ttn')['order_id'],
                new FormLabelRequestBuilder($request),
                (int)$request->get('options')['autoTracking'] === 1
            );

            $downloads = [];
            foreach (['a4', 'm85', 'm100'] as $format) {
                $downloads[] = [
                    'format' => $format,
                    'url' => admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $response->id . '&format=' . $format),
                ];
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'tracking_number' => $response->trackingNumber,
                    'shipment_cost' => $response->shipmentCost,
                    'estimated_delivery_date' => $response->estimatedDeliveryDate->format('Y-m-d'),
                    'order_url' => get_admin_url( null, 'post.php?post=' . $response->orderId . '&action=edit'),
                    'downloads' => $downloads,
                ]
            ]);
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => 0,
                    'message' => $e->getMessage(),
                ],
            ]);
        }
    }

    public function createLabelBatch(Request $request): ResponseInterface
    {
        $order = wc_get_order((int)$request->get('orderId'));
        if ($order === null) {
            throw new \Exception("Order " . (int)$request->get('orderId') . " not found");
        }

        try {
            $response = $this->smartyParcelService->createLabel(
                $order->get_id(),
                new OrderLabelRequestBuilder($order),
                (int)$request->get('options')['autoTracking'] === 1
            );

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'label_id' => $response->labelId,
                    'tracking_number' => $response->trackingNumber,
                    'shipment_cost' => $response->shipmentCost,
                    'estimated_delivery_date' => $response->estimatedDeliveryDate->format('Y-m-d'),
                    'tracking_status' => $response->trackingStatus,
                ]
            ]);
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details' => $e->getDetails(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => 0,
                    'message' => $e->getMessage(),
                ],
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

            // We can't void legacy WCUS Pro labels yet
            if ($label['carrier_slug'] !== 'wcus_pro') {
                $this->api->voidLabel($label['label_id']);
            }
            $this->shippingLabelsRepository->deleteById((int)$label['id']);

            $this->automationService->executeEvent(
                AutomationService::EVENT_LABEL_VOIDED,
                new Context(
                    AutomationService::EVENT_LABEL_CREATED,
                    wc_get_order((int)$label['order_id']),
                    [
                        'tracking_number' => $label['tracking_number'],
                        'carrier_status' => $label['carrier_status']
                    ]
                )
            );

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

    public function upgradePlan(Request $request): ResponseInterface
    {
        try {
            $token = $this->smartyParcelService->createOneTimeUpgradeAction($request->get('subscription'));

            return $this->jsonResponse([
                'success' => true,
                'redirect' => 'https://app.smartyparcel.com/login/one-time/' . $token
            ]);
        } catch (\Exception $e) {
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
