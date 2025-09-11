<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Helpers;

final class SmartyParcelHelper
{
    public static function isConnected(): bool
    {
        return get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) === 'connected';
    }

    public static function canPurchaseLabelForOrder(\WC_Order $order): bool
    {
        $forbiddenMethods = apply_filters('wcus_labels_forbidden_methods', []);

        foreach ($forbiddenMethods as $method) {
            if ($order->has_shipping_method($method)) {
                return false;
            }
        }

        return true;
    }
}
