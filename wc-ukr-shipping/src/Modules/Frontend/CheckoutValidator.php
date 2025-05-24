<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Component\Validation\CheckoutValidatorInterface;
use kirillbdev\WCUkrShipping\Component\Validation\NovaPoshtaCheckoutValidator;
use kirillbdev\WCUkrShipping\Component\Validation\UkrposhtaCheckoutValidator;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

if ( ! defined('ABSPATH')) {
    exit;
}

class CheckoutValidator implements ModuleInterface
{
    public function init(): void
    {
        add_action('woocommerce_checkout_process', [$this, 'validateFields']);
        add_filter('woocommerce_checkout_fields', [$this, 'removeDefaultFieldsFromValidation'], 99);
    }

    public function removeDefaultFieldsFromValidation(array $fields): array
    {
        if ( ! wp_doing_ajax() || empty($_POST)) {
            return $fields;
        }

        if ($this->isPluginShippingMethodSelected()) {
            if ($this->maybeDisableDefaultFields()) {
                foreach (['billing', 'shipping'] as $type) {
                    unset($fields[$type][$type . '_address_1']);
                    unset($fields[$type][$type . '_address_2']);
                    unset($fields[$type][$type . '_city']);
                    unset($fields[$type][$type . '_state']);
                    unset($fields[$type][$type . '_postcode']);
                }
            }
        }

        return $fields;
    }

    public function validateFields(): void
    {
        if ($this->isPluginShippingMethodSelected() && $this->checkoutValidationActive()) {
           $validator = $this->getCheckoutValidator();
           if ($validator !== null) {
               $validator->validate($_POST);
           }
        }
    }

    /**
     * @return bool
     */
    private function maybeDisableDefaultFields()
    {
        return apply_filters('wc_ukr_shipping_prevent_disable_default_fields', false) === false;
    }

    private function isPluginShippingMethodSelected(): bool
    {
        $pluginShippingMethods = [
            WC_UKR_SHIPPING_NP_SHIPPING_NAME,
            'wcus_ukrposhta_shipping',
        ];

        foreach ($pluginShippingMethods as $method) {
            if (WCUSHelper::hasChosenShippingMethod($method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function checkoutValidationActive()
    {
        return true === apply_filters('wcus_checkout_validation_active', true);
    }

    private function getCheckoutValidator(): ?CheckoutValidatorInterface
    {
        if (WCUSHelper::hasChosenShippingMethod(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            return new NovaPoshtaCheckoutValidator();
        } elseif (WCUSHelper::hasChosenShippingMethod('wcus_ukrposhta_shipping')) {
            return new UkrposhtaCheckoutValidator();
        }

        return null;
    }
}
