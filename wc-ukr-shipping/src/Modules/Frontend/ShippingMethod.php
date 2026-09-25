<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Component\Block\ShippingBlockIntegration;
use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Foundation\GLSAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\GLSShipping;
use kirillbdev\WCUkrShipping\Foundation\InPostAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\InPostShipping;
use kirillbdev\WCUkrShipping\Foundation\NovaGlobalAddress;
use kirillbdev\WCUkrShipping\Foundation\NovaPoshtaShipping;
use kirillbdev\WCUkrShipping\Foundation\NovaPostAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\NovaPostShipping;
use kirillbdev\WCUkrShipping\Foundation\PostNordAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\PostNordShipping;
use kirillbdev\WCUkrShipping\Foundation\RozetkaDeliveryShipping;
use kirillbdev\WCUkrShipping\Foundation\MeestShipping;
use kirillbdev\WCUkrShipping\Foundation\MeestAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\UkrPoshtaAddressShipping;
use kirillbdev\WCUkrShipping\Foundation\UkrPoshtaShipping;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class ShippingMethod implements ModuleInterface
{
    /**
     * Boot function
     *
     * @return void
     */
    public function init()
    {
        add_filter('woocommerce_shipping_methods', [ $this, 'registerShippingMethod' ]);
        add_filter('woocommerce_cart_shipping_packages', [$this, 'calculatePackageRateHash']);
        add_filter('woocommerce_cart_shipping_method_full_label', [$this, 'appendViewCostToRateLabel'], 10, 2);
        add_filter('woocommerce_order_shipping_to_display', [$this, 'displayOrderViewCost'], 10, 2);
        add_filter('woocommerce_get_order_item_totals', [$this, 'preventEmailFreeShippingLabel'], 10, 2);

        // test
        add_action( 'woocommerce_blocks_loaded', function () {

            add_action(
                'woocommerce_blocks_checkout_block_registration',
                function ( $integration_registry ) {
                    $integration_registry->register(
                        new ShippingBlockIntegration()
                    );
                }
            );
        });
    }

    public function registerShippingMethod($methods)
    {
        $activeCarriers = WCUSHelper::safeGetJsonOption('wcus_active_carriers');
        if (in_array(CarrierSlug::NOVA_POSHTA, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_NOVA_POSHTA] = NovaPoshtaShipping::class;
        }
        if (in_array(CarrierSlug::UKRPOSHTA, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_UKRPOSHTA] = UkrPoshtaShipping::class;
            $methods[WCUS_SHIPPING_METHOD_UKRPOSHTA_ADDRESS] = UkrPoshtaAddressShipping::class;
        }
        if (in_array(CarrierSlug::ROZETKA_DELIVERY, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_ROZETKA] = RozetkaDeliveryShipping::class;
        }
        if (in_array(CarrierSlug::NOVA_POST, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_NOVA_POST] = NovaPostShipping::class;
            $methods[WCUS_SHIPPING_METHOD_NOVA_POST_ADDRESS] = NovaPostAddressShipping::class;
        }
        if (in_array(CarrierSlug::NOVA_GLOBAL, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_NOVA_GLOBAL_ADDRESS] = NovaGlobalAddress::class;
        }
        if (in_array(CarrierSlug::MEEST, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_MEEST] = MeestShipping::class;
            $methods[WCUS_SHIPPING_METHOD_MEEST_ADDRESS] = MeestAddressShipping::class;
        }
        if (in_array(CarrierSlug::POST_NORD, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_POST_NORD] = PostNordShipping::class;
            $methods[WCUS_SHIPPING_METHOD_POST_NORD_ADDRESS] = PostNordAddressShipping::class;
        }
        if (in_array(CarrierSlug::INPOST, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_INPOST] = InPostShipping::class;
            $methods[WCUS_SHIPPING_METHOD_INPOST_ADDRESS] = InPostAddressShipping::class;
        }
        if (in_array(CarrierSlug::GLS, $activeCarriers)) {
            $methods[WCUS_SHIPPING_METHOD_GLS] = GLSShipping::class;
            $methods[WCUS_SHIPPING_METHOD_GLS_ADDRESS] = GLSAddressShipping::class;
        }

        return $methods;
    }

    public function calculatePackageRateHash(array $packages): array
    {
        // We need to perform calculation only for ajax refresh checkout and place order
        if (!isset($_GET['wc-ajax'])
            || !in_array($_GET['wc-ajax'], ['update_order_review', 'checkout'], true)) {
            return $packages;
        }

        $chosenMethods = wc_get_chosen_shipping_method_ids();
        $supportedMethods = WCUSHelper::getPluginShippingMethodIds();
        foreach ($packages as $key => &$package) {
            if (isset($chosenMethods[$key])
                && in_array($chosenMethods[$key], $supportedMethods, true)) {
                $package['wcus_rates_hash'] = $this->getRatesHash($chosenMethods[$key]);
            }
        }

        return $packages;
    }

    private function getRatesHash(string $methodId): string
    {
        $data = WCUSHelper::getCheckoutPostData();
        $fields = array_filter(
            $data,
            fn($key) => strpos((string)$key, 'wcus_') === 0,
            ARRAY_FILTER_USE_KEY
        );
        ksort($fields);

        return md5((string)wp_json_encode([
            'method' => $methodId,
            'payment_method' => $data['payment_method'] ?? '',
            'fields' => $fields,
        ]));
    }

    /**
     * The rate cost is zero in view only mode, so the calculated cost is rendered from the rate meta.
     *
     * @param string $label
     * @param \WC_Shipping_Rate $rate
     */
    public function appendViewCostToRateLabel($label, $rate): string
    {
        $viewCost = WCUSHelper::getRateViewCost($rate);

        return $viewCost === null
            ? $label
            : $label . ': ' . wc_price($viewCost);
    }

    /**
     * @param string $shipping
     * @param \WC_Order $order
     */
    public function displayOrderViewCost($shipping, $order): string
    {
        $viewCost = WCUSHelper::getOrderViewShippingCost($order);

        return $viewCost === null
            ? $shipping
            : wc_price($viewCost, ['currency' => $order->get_currency()]) . ' (' . $shipping . ')';
    }

    /**
     * Since WooCommerce 11.1 emails replace the shipping value with "Free!" when it equals the method name
     * (zero shipping total), so make the value differ from the method name for plugin shipping methods.
     *
     * @param array $totalRows
     * @param \WC_Order $order
     */
    public function preventEmailFreeShippingLabel($totalRows, $order): array
    {
        if (
            !isset($totalRows['shipping']['value'], $totalRows['shipping']['meta'])
            || $totalRows['shipping']['value'] !== $totalRows['shipping']['meta']
            || !WCUSHelper::orderHasPluginShippingMethod($order)
        ) {
            return $totalRows;
        }

        $totalRows['shipping']['value'] = '<span class="wcus-shipping-method">' . $totalRows['shipping']['value'] . '</span>';

        return $totalRows;
    }
}
