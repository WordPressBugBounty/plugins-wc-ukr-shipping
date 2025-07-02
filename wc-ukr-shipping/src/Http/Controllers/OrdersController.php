<?php

namespace kirillbdev\WCUkrShipping\Http\Controllers;

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
}
