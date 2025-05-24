<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Foundation\NovaPoshtaShipping;
use kirillbdev\WCUkrShipping\Foundation\UkrPoshtaShipping;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\TranslateService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class ShippingMethod implements ModuleInterface
{
    private static ?string $cachedRateHash = null;

    private TranslateService $translateService;

    public function __construct(TranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    /**
     * Boot function
     *
     * @return void
     */
    public function init()
    {
        add_filter('woocommerce_shipping_methods', [ $this, 'registerShippingMethod' ]);
        add_filter('woocommerce_shipping_rate_label', [ $this, 'getRateLabel' ], 10, 2);
        add_filter('woocommerce_cart_shipping_packages', [$this, 'calculatePackageRateHash']);
        add_filter('woocommerce_calculated_total', [$this, 'calculateCartTotal'], 10, 2);
    }

    public function registerShippingMethod($methods)
    {
        $methods[WC_UKR_SHIPPING_NP_SHIPPING_NAME] = NovaPoshtaShipping::class;
        $methods['wcus_ukrposhta_shipping'] = UkrPoshtaShipping::class;

        return $methods;
    }

    public function getRateLabel($label, $rate)
    {
        if (WC_UKR_SHIPPING_NP_SHIPPING_NAME === $rate->get_method_id()) {
            $label = $this->translateService->getTranslates()['method_title'];
        }

        return $label;
    }

    public function calculatePackageRateHash(array $packages): array
    {
        // We need to perform calculation only for ajax refresh checkout and place order
        if (!isset($_GET['wc-ajax'])
            || !in_array($_GET['wc-ajax'], ['update_order_review', 'checkout'], true)) {
            return $packages;
        }

        $chosenMethods = wc_get_chosen_shipping_method_ids();
        foreach ($packages as $key => &$package) {
            if (isset($chosenMethods[$key])
                && in_array($chosenMethods[$key], [WC_UKR_SHIPPING_NP_SHIPPING_NAME, 'wcus_ukrposhta_shipping'], true)) {
                // todo: bad solution! provide array cache implementation instead
                if (self::$cachedRateHash === null) {
                    self::$cachedRateHash = md5(
                        sprintf('%s_%f', $chosenMethods[$key], microtime(true))
                    );
                }
                $package['wcus_rates_hash'] = self::$cachedRateHash;
            }
        }

        return $packages;
    }

    public function calculateCartTotal(float $total, \WC_Cart $cart): float
    {
        $costViewOnly = false;
        if (WCUSHelper::hasChosenShippingMethod(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            $costViewOnly = (int)get_option('wcus_cost_view_only') === 1;
        } elseif (WCUSHelper::hasChosenShippingMethod('wcus_ukrposhta_shipping')) {
            $costViewOnly = (int)get_option('wcus_ukrposhta_cost_view_only') === 1;
        }

        return $costViewOnly
            ? $total - $cart->get_shipping_total()
            : $total;
    }
}
