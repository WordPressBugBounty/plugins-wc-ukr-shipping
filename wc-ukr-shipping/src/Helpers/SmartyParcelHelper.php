<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Helpers;

use kirillbdev\WCUkrShipping\Enums\CarrierSlug;

final class SmartyParcelHelper
{
    public static function isConnected(): bool
    {
        return get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) === 'connected';
    }

    /**
     * Connected without an account: the key gives access to the locator only.
     */
    public static function isGuest(): bool
    {
        return get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) === 'guest';
    }

    public static function hasLocatorAccess(): bool
    {
        return self::isConnected() || self::isGuest();
    }

    /**
     * OAuth URL that connects the store to a full SmartyParcel account (also upgrades a guest).
     * After the connection the admin is sent back to $returnTo (an admin URL), or to the dashboard.
     */
    public static function getConnectUrl(string $returnTo = ''): string
    {
        $callbackUrl = admin_url('admin.php?page=wcus_oauth_callback');
        if ($returnTo !== '') {
            $callbackUrl = add_query_arg('return_to', rawurlencode($returnTo), $callbackUrl);
        }

        $session = [
            'storeUrl' => self::getStoreUrl(),
            'callbackUrl' => $callbackUrl,
            'platform' => 'woocommerce',
            'appName' => 'wc-ukr-shipping',
            'nonce' => wp_create_nonce('smartyparcel_oauth'),
        ];
        // Only the guest id: session data goes through the browser, the guest key never does
        if (self::isGuest()) {
            $session['guestId'] = (string)get_option(WCUS_OPTION_SMARTY_PARCEL_GUEST_ID);
        }

        return 'https://app.smartyparcel.com/oauth-session?session_data=' . urlencode(base64_encode(json_encode($session)));
    }

    /**
     * Explains why a feature that needs a full account is unavailable, for both guest and disconnected stores.
     */
    public static function getAccountRequiredMessage(): string
    {
        if (self::isGuest()) {
            return __('Your store is connected to SmartyParcel in limited mode, which only covers pickup point search. This feature requires a full SmartyParcel account.', 'wc-ukr-shipping');
        }

        return __('This feature requires a SmartyParcel account. Connect your store to an account to continue.', 'wc-ukr-shipping');
    }

    /**
     * Only an administrator can finish the OAuth callback, see SmartyParcel::handleOauthCallback().
     */
    public static function currentUserCanConnect(): bool
    {
        return current_user_can('manage_options');
    }

    /**
     * The one store URL the plugin sends to the platform. A guest key is bound to it,
     * so every request must use exactly the same value.
     */
    public static function getStoreUrl(): string
    {
        return rtrim(get_site_url(), '/');
    }

    public static function canPurchaseLabelForOrder(\WC_Order $order): bool
    {
        $forbiddenMethods = apply_filters('wcus_labels_forbidden_methods', [
            'local_pickup',
        ]);

        foreach ($forbiddenMethods as $method) {
            if ($order->has_shipping_method($method)) {
                return false;
            }
        }

        return true;
    }

    public static function getOrderCarrierSlug(\WC_order $order): ?string
    {
        $orderShipping = WCUSHelper::getOrderShippingMethod($order);
        if ($orderShipping === null) {
            return null;
        }

        return self::getCarrierFromShippingMethod($orderShipping->get_method_id());
    }

    public static function getCarrierFromShippingMethod(string $shippingMethodId): ?string
    {
        switch ($shippingMethodId) {
            case WCUS_SHIPPING_METHOD_NOVA_POSHTA:
                return CarrierSlug::NOVA_POSHTA;
            case WCUS_SHIPPING_METHOD_UKRPOSHTA:
            case WCUS_SHIPPING_METHOD_UKRPOSHTA_ADDRESS:
                return CarrierSlug::UKRPOSHTA;
            case WCUS_SHIPPING_METHOD_ROZETKA:
                return CarrierSlug::ROZETKA_DELIVERY;
            case WCUS_SHIPPING_METHOD_NOVA_POST:
            case WCUS_SHIPPING_METHOD_NOVA_POST_ADDRESS:
                return CarrierSlug::NOVA_POST;
            case WCUS_SHIPPING_METHOD_NOVA_GLOBAL_ADDRESS:
                return CarrierSlug::NOVA_GLOBAL;
            case WCUS_SHIPPING_METHOD_MEEST:
            case WCUS_SHIPPING_METHOD_MEEST_ADDRESS:
                    return CarrierSlug::MEEST;
            case WCUS_SHIPPING_METHOD_DHL_EXPRESS_ADDRESS:
                return CarrierSlug::DHL_EXPRESS;
        }

        return null;
    }

    public static function getOrderStateName(string $countryCode, string $stateCode): string
    {
        $states = WC()->countries->get_states($countryCode);
        if (empty($states) || !is_array($states)) {
            return $stateCode;
        }

        return $states[$stateCode] ?? $stateCode;
    }
}
