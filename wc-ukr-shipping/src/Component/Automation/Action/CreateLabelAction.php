<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Automation\Action;

use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\OrderLabelRequestBuilder;
use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;

if ( ! defined('ABSPATH')) {
    exit;
}

class CreateLabelAction implements ActionInterface
{
    private SmartyParcelService $smartyParcelService;

    public function __construct()
    {
        $this->smartyParcelService = wcus_container()->make(SmartyParcelService::class);
    }

    public function execute(Context $context): void
    {
        $order = $context->getOrder();
        $shippingMethod = WCUSHelper::getOrderShippingMethod($order);
        if ($shippingMethod === null || $shippingMethod->get_method_id() !== WCUS_SHIPPING_METHOD_NOVA_POSHTA) {
            return;
        }
        if ($this->smartyParcelService->getLabelByOrderId($order->get_id()) !== null) {
            return;
        }

        try {
            $this->smartyParcelService->createLabel(
                CarrierSlug::NOVA_POSHTA,
                $order->get_id(),
                new OrderLabelRequestBuilder($order)
            );
        } catch (SmartyParcelErrorException $e) {
            $order->add_meta_data(
                '_wcus_automation_error',
                sprintf(
                    '[Automation] Error creating label: Source: SmartyParcel | Error: [%d] %s',
                    $e->getCode(),
                    $e->getMessage()
                )
            );
            $order->save_meta_data();
        } catch (\Throwable $e) {
            $order->add_meta_data(
                '_wcus_automation_error',
                sprintf('[Automation] Error creating label: Source: Internal | Error: %s', $e->getMessage())
            );
            $order->save_meta_data();
        }
    }
}
