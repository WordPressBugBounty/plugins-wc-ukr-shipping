<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Foundation\NovaPoshtaShipping;
use kirillbdev\WCUkrShipping\Model\CheckoutOrderData;
use kirillbdev\WCUkrShipping\Services\TranslateService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class ShippingMethod implements ModuleInterface
{
    /**
     * @var TranslateService
     */
    private $translateService;

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
        $orderData = null;
        if (isset($_GET['wc-ajax'])) {
            if ($_GET['wc-ajax'] === 'update_order_review' && ! empty($_POST['post_data'])) {
                parse_str(sanitize_text_field($_POST['post_data']), $post);
                $orderData = new CheckoutOrderData($post);
            } elseif ($_GET['wc-ajax'] === 'checkout') {
                $orderData = new CheckoutOrderData($_POST);
            }
        }

        $chosenMethods = wc_get_chosen_shipping_method_ids();
        foreach ($packages as $key => &$package) {
            if (isset($chosenMethods[$key]) && $chosenMethods[$key] === WC_UKR_SHIPPING_NP_SHIPPING_NAME
                && $orderData !== null) {
                $shippingType = $orderData->isAddressShipping() ? 'doors' : 'warehouse';
                if ($orderData->getShippingType() !== null) {
                    $shippingType = $orderData->getShippingType();
                }

                $package['wcus_rates_hash'] = md5(
                    sprintf(
                        'wcus_rates:%s|%s|%s',
                        $orderData->getShippingAddress()->getCityRef(),
                        $orderData->getPaymentMethod(),
                        $shippingType
                    )
                );
            }
        }

        return $packages;
    }

    public function calculateCartTotal(float $total, \WC_Cart $cart): float
    {
        if ( ! in_array(WC_UKR_SHIPPING_NP_SHIPPING_NAME, wc_get_chosen_shipping_method_ids(), true)) {
            return $total;
        }

        if ((int)get_option('wcus_cost_view_only') === 1) {
            return $total - $cart->get_shipping_total();
        }

        return $total;
    }
}