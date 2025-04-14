<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

use kirillbdev\WCUkrShipping\Exceptions\ApiServiceException;
use kirillbdev\WCUkrShipping\Services\OrderService;
use kirillbdev\WCUSCore\Http\Contracts\ResponseInterface;
use kirillbdev\WCUSCore\Http\Controller;
use kirillbdev\WCUSCore\Http\Request;

if ( ! defined('ABSPATH')) {
    exit;
}

class OrdersController extends Controller
{
    private OrderService $orderService;

    public function __construct(
        OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    public function getOrders(Request $request): ResponseInterface
    {
        return $this->jsonResponse([
            'success' => true,
            'data' => [
                'orders' => $this->orderService->getOrdersFromRequest($request),
                'count_pages' => $this->orderService->getCountPagesFromRequest($request)
            ]
        ]);
    }

    public function generateTTN(Request $request): ResponseInterface
    {
        try {
            $invoice = $this->autoInvoiceOperation->createInvoiceFromOrderId($request->get('order_id', 0));
            // Trackings API
            $autoTracking = $request->get('options', [])['autoTracking'] ?? 0;
            if ((int)$autoTracking === 1) {
                $this->trackingsMockService->createTracking((string)$invoice->documentNumber);
            }

            // Execute automation
            $ttn = $this->ttnRepository->findById($invoice->id);
            $order = wc_get_order((int)$request->get('order_id'));
            if ($ttn && $order) {
                $this->automationService->executeEvent(
                    'ttn_created',
                    new Context('ttn_created', $order, (array)$ttn)
                );
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'ttn_id' => $invoice->documentNumber,
                    'ttn_db_id' => $invoice->id,
                    'ttn_ref' => $invoice->ref,
                    // todo: next line should be refactored
                    'carrier_status' => '1',
                    'carrier_status_additional' => 'Відправник самостійно створив цю накладну, але ще не надав до відправки',
                ]
            ]);
        }
        catch (ApiServiceException $e) {
            return $this->jsonResponse([
                'success' => false,
                'errors' => [
                    $e->getMessage()
                ]
            ]);
        }
        catch (ApiException $e) {
            return $this->jsonResponse([
                'success' => false,
                'errors' => $e->getErrors()
            ]);
        }
    }
}