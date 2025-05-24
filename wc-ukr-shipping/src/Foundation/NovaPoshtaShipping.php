<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Factories\Rates\NovaPoshta\NovaPoshtaCheckoutOrderFactory;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\CalculationService;
use kirillbdev\WCUkrShipping\Services\TranslateService;

if ( ! defined('ABSPATH')) {
    exit;
}

class NovaPoshtaShipping extends \WC_Shipping_Method
{
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = WC_UKR_SHIPPING_NP_SHIPPING_NAME;
        $this->method_title = WC_UKR_SHIPPING_NP_SHIPPING_TITLE;
        $this->method_description = 'Nova Poshta by WC Ukraine Shipping';

        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        );

        $this->init();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init()
    {
        $this->init_settings();
        $this->init_form_fields();

        $translator = new TranslateService();
        $translates = $translator->getTranslates();

        $this->title = $translates['method_title'];

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields(): void
    {
        $this->instance_form_fields = [
            'options_message' => [
                'type' => 'wcus_message',
                'text' => __('You can manage shipping method options at', 'wc-ukr-shipping-i18n'),
                'link' => home_url('wp-admin/admin.php?page=wc_ukr_shipping_options'),
            ],
        ];
    }

    public function generate_wcus_message_html(string $key, array $data): string
    {
        $html = sprintf(
            '<div>%s <a href="%s" target="_blank">%s</a></div>',
            esc_html($data['text']),
            esc_attr($data['link']),
            __('plugin settings page', 'wc-ukr-shipping-i18n')
        );

        return $html;
    }

    public function calculate_shipping($package = []): void
    {
        if ( ! $this->shouldCalculated()) {
            $this->add_rate([
                'label' => $this->title,
                'cost' => 0,
                'package' => $package,
            ]);
            return;
        }

        $service = new CalculationService();
        $factory = new NovaPoshtaCheckoutOrderFactory();

        $rate = [
            'label' => $this->title,
            'cost' => $service->calculateRates($factory->createOrderInfo()),
            'package' => $package,
        ];
        $this->add_rate($rate);
    }

    /**
     * Is this method available?
     * @param array $package
     * @return bool
     */
    public function is_available($package)
    {
        return $this->is_enabled();
    }

    private function shouldCalculated(): bool
    {
        if ( ! isset($_GET['wc-ajax'])) {
            return false;
        }

        if (!WCUSHelper::hasChosenShippingMethod(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            return false;
        }

        return ($_GET['wc-ajax'] === 'update_order_review' && ! empty($_POST['post_data']))
            || $_GET['wc-ajax'] === 'checkout';
    }
}
