<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Contracts\Order\OrderHandlerInterface;
use kirillbdev\WCUkrShipping\Contracts\Order\OrderShippingHandlerInterface;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUkrShipping\Component\Carriers\NovaPoshta\Order\CheckoutOrderHandler as NovaPoshtaCheckoutOrderHandler;
use kirillbdev\WCUkrShipping\Component\Carriers\NovaPoshta\Order\CheckoutOrderShippingHandler as NovaPoshtaCheckoutOrderShippingHandler;
use kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Order\CheckoutOrderHandler as UkrposhtaCheckoutOrderHandler;
use kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Order\CheckoutOrderShippingHandler as UkrposhtaCheckoutOrderShippingHandler;

if ( ! defined('ABSPATH')) {
    exit;
}

class OrderCreator implements ModuleInterface
{
    public function init(): void
    {
        if (is_admin()) {
            return;
        }

        add_action('woocommerce_checkout_create_order', [ $this, 'createOrder' ]);
        add_action('woocommerce_checkout_create_order_shipping_item', [ $this, 'saveOrderShipping' ]);
    }

    public function createOrder(\WC_Order $order): void
    {
        $handler = $this->createOrderHandler($order);
        if ($handler !== null) {
            $handler->saveShippingData($order, $_POST);
        }
    }

    /**
     * @param \WC_Order_Item_Shipping $item
     */
    public function saveOrderShipping($item)
    {
        $handler = $this->createOrderShippingHandler($item->get_method_id());
        if ($handler !== null) {
            $handler->saveShippingData($item, $_POST);
        }
    }

    private function createOrderHandler(\WC_Order $order): ?OrderHandlerInterface
    {
        if ($order->has_shipping_method(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            return new NovaPoshtaCheckoutOrderHandler();
        } elseif ($order->has_shipping_method('wcus_ukrposhta_shipping')) {
            return new UkrposhtaCheckoutOrderHandler();
        }

        return null;
    }

    private function createOrderShippingHandler(string $shippingMethod): ?OrderShippingHandlerInterface
    {
        $fieldGroup = $this->isShipToDifferentAddress() ? 'shipping' : 'billing';
        if ($shippingMethod === WC_UKR_SHIPPING_NP_SHIPPING_NAME) {
            return new NovaPoshtaCheckoutOrderShippingHandler($fieldGroup);
        } elseif ($shippingMethod === 'wcus_ukrposhta_shipping') {
            return new UkrposhtaCheckoutOrderShippingHandler($fieldGroup);
        }

        return null;
    }

    private function isShipToDifferentAddress(): bool
    {
        return isset($_POST['ship_to_different_address'])
            && (int)$_POST['ship_to_different_address'] === 1;
    }
}
