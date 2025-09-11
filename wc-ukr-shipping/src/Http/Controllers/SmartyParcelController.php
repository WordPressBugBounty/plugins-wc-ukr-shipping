<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Component\Carriers\RozetkaDelivery\Label\BatchLabelRequestAdapter;
use kirillbdev\WCUkrShipping\Component\Carriers\RozetkaDelivery\Label\PurchaseLabelDataCollector;
use kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Label\UkrposhtaBatchLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Label\UkrposhtaFormLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\FormLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\OrderLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\ProxyLabelRequestBuilder;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\AutomationService;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

class SmartyParcelController extends Controller
{
    private SmartyParcelApi $api;
    private SmartyParcelWPApi $spApi;
    private SmartyParcelService $smartyParcelService;
    private ShippingLabelsRepository $shippingLabelsRepository;
    private AutomationService $automationService;

    public function __construct(
        SmartyParcelApi $api,
        SmartyParcelWPApi $spApi,
        SmartyParcelService $smartyParcelService,
        ShippingLabelsRepository $shippingLabelsRepository,
        AutomationService $automationService
    ) {
        $this->api = $api;
        $this->spApi = $spApi;
        $this->smartyParcelService = $smartyParcelService;
        $this->shippingLabelsRepository = $shippingLabelsRepository;
        $this->automationService = $automationService;
    }

    public function sendApiRequest(Request $request): ResponseInterface
    {
        try {
            return $this->jsonResponse([
                'success' => true,
                'data' => $this->spApi->sendRequest($request->get('route'), $request->get('payload')),
            ]);
        } catch (SmartyParcelErrorException $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'details ' => $e->getDetails(),
                ]
            ]);
        }
    }

    public function disconnect(Request $request): ResponseInterface
    {
        try {
            $this->smartyParcelService->tryDisconnectApplication();

            delete_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
            delete_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS);
            delete_option('wcus_nova_poshta_default_carrier');
            delete_option('wcus_ukrposhta_default_carrier');
            delete_transient('smarty_parcel_acc');

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

    public function connectCarrier(Request $request): ResponseInterface
    {
        try {
            $response = $this->api->connectCarrier($request->get('carrier_slug'), $request->get('account_data'));
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

    /**
     * @deprecated Will be replaced by purchaseLabel
     * @param Request $request
     * @return ResponseInterface
     */
    public function createShippingLabel(Request $request): ResponseInterface
    {
        try {
            $builder = null;
            if ($request->get('carrier') === 'nova_poshta') {
                $builder = new FormLabelRequestBuilder($request);
            } elseif ($request->get('carrier') === 'ukrposhta') {
                $builder = new UkrposhtaFormLabelRequestBuilder($request);
            }

            $response = $this->smartyParcelService->createLabel(
                $request->get('carrier'),
                (int)$request->get('ttn')['order_id'],
                $builder,
                (int)$request->get('options')['autoTracking'] === 1
            );

            $formats = WCUSHelper::getLabelDownloadFormats($request->get('carrier'));
            $downloads = [];
            foreach ($formats as $format => $Name) {
                $downloads[] = [
                    'format' => $Name,
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

    public function purchaseLabel(Request $request): ResponseInterface
    {
        try {
            $response = $this->smartyParcelService->createLabel(
                'rozetka_delivery',
                (int)$request->get('order_id'),
                new ProxyLabelRequestBuilder($request->get('request')),
                true
            );

            $formats = WCUSHelper::getLabelDownloadFormats('rozetka_delivery');
            $downloads = [];
            foreach ($formats as $format => $name) {
                $downloads[] = [
                    'format' => $name,
                    'url' => admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $response->id . '&format=' . $format),
                ];
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'tracking_number' => $response->trackingNumber,
                    'shipment_cost' => $response->shipmentCost,
                    'estimated_delivery_date' => $response->estimatedDeliveryDate !== null
                        ? $response->estimatedDeliveryDate->format('Y-m-d')
                        : null,
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

    public function attachShippingLabel(Request $request): ResponseInterface
    {
        try {
            $this->smartyParcelService->attachLabel(
                $request->get('carrierSlug'),
                $request->get('trackingNumber'),
                (int)$request->get('orderId'),
                $request->get('addToTracking') === 'true'
            );

            return $this->jsonResponse([
                'success' => true,
            ]);
        } catch (\Exception $e) {
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
        try {
            $order = wc_get_order((int)$request->get('orderId'));
            if ($order === null) {
                throw new \Exception("Order " . (int)$request->get('orderId') . " not found");
            }

            $shippingMethod = WCUSHelper::getOrderShippingMethod($order);
            if ($shippingMethod === null) {
                throw new \Exception('Unable to get order shipping method');
            }

            switch ($shippingMethod->get_method_id()) {
                case WC_UKR_SHIPPING_NP_SHIPPING_NAME:
                    $carrier = CarrierSlug::NOVA_POSHTA;
                    $builder = new OrderLabelRequestBuilder($order);
                    break;
                case WCUS_SHIPPING_METHOD_UKRPOSHTA:
                    $carrier = CarrierSlug::UKRPOSHTA;
                    $builder = new UkrposhtaBatchLabelRequestBuilder($order);
                    break;
                case WCUS_SHIPPING_METHOD_ROZETKA:
                    $carrier = CarrierSlug::ROZETKA_DELIVERY;
                    $builder = new BatchLabelRequestAdapter(new PurchaseLabelDataCollector($order));
                    break;
                default:
                    throw new \Exception('Carrier not supported');
            }

            $response = $this->smartyParcelService->createLabel(
                $carrier,
                $order->get_id(),
                $builder,
                (int)$request->get('options')['autoTracking'] === 1
            );

            $downloads = [];
            foreach (WCUSHelper::getLabelDownloadFormats($carrier) as $format => $name) {
                $downloads[] = [
                    'name' => $name,
                    'url' => admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $response->id . '&format=' . $format),
                ];
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'label_id' => $response->labelId,
                    'tracking_number' => $response->trackingNumber,
                    'shipment_cost' => $response->shipmentCost,
                    'estimated_delivery_date' => $response->estimatedDeliveryDate !== null
                        ? $response->estimatedDeliveryDate->format('Y-m-d')
                        : null,
                    'tracking_status' => $response->trackingStatus,
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

    public function voidLabel(Request $request): ResponseInterface
    {
        try {
            $label = $this->shippingLabelsRepository->findById((int)$request->get('label_id'));
            if ($label === null) {
                throw new \Exception('Label by id ' . $request->get('label_id') . ' not found');
            }

            // We can't void legacy WCUS Pro labels or attached labels yet
            if ($label['label_id']) {
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
