<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Carriers\DhlExpress\Order;

use kirillbdev\WCUkrShipping\Contracts\Order\OrderShippingHandlerInterface;
use kirillbdev\WCUkrShipping\Foundation\DhlExpressAddressShipping;
use kirillbdev\WCUkrShipping\Traits\SafeOrderShippingTrait;

if ( ! defined('ABSPATH')) {
    exit;
}

class CheckoutOrderShippingHandler implements OrderShippingHandlerInterface
{
    use SafeOrderShippingTrait;

    public function saveShippingData(\WC_Order_Item_Shipping $item, array $data): void
    {
        // Keep the service in the order, so later changes of the method settings do not affect it
        $shippingMethod = new DhlExpressAddressShipping((int)$item->get_instance_id());
        $this->updateMeta($item, WCUS_SHIPPING_META_SERVICE_TYPE, (string)$shippingMethod->get_option('service_type'));
    }
}
