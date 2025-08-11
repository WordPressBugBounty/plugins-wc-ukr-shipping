<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Factories\Rates\Ukrposhta\UkrposhtaCheckoutOrderFactory;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\CalculationService;

if ( ! defined('ABSPATH')) {
    exit;
}

class UkrPoshtaShipping extends \WC_Shipping_Method
{
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = WCUS_SHIPPING_METHOD_UKRPOSHTA;
        $this->method_title = __('Ukrposhta', 'wc-ukr-shipping-i18n');
        $this->method_description = 'Ukrposhta by WC Ukraine Shipping';

        $this->supports = [
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

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

        $this->title = $this->get_option('title');

        // @phpstan-ignore-next-line
        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields(): void
    {
        $this->instance_form_fields = [
            'title' => [
                'title' => __('Name', 'woocommerce' ),
                'type' => 'text',
                'description' => '',
                'default' => __('Ukrposhta', 'wc-ukr-shipping-i18n') . ' ' . __($this->get_option('service_type'), 'wc-ukr-shipping-i18n'),
            ],
            'service_type' => [
                'title' => __('Type of shipment', 'wc-ukr-shipping-i18n'),
                'type' => 'select',
                'description' => '',
                'default' => 'EXPRESS',
                'options' => [
                    'STANDARD' => __('STANDARD', 'wc-ukr-shipping-i18n'),
                    'EXPRESS'  => __('EXPRESS', 'wc-ukr-shipping-i18n'),
                ],
            ],
            'manage_options' => [
                'type' => 'wcus_message',
                'text' => __('You can manage shipping method options at', 'wc-ukr-shipping-i18n'),
                'link' => home_url('wp-admin/admin.php?page=wc_ukr_shipping_options&section=ukrposhta'),
            ],
        ];
    }

    public function generate_wcus_message_html(string $key, array $data): string
    {
        $html = sprintf(
            '<div style="margin-bottom: 16px;">%s <a href="%s" target="_blank">%s</a></div>',
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
        $factory = new UkrposhtaCheckoutOrderFactory($this->get_option('service_type'));

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

        if (!WCUSHelper::hasChosenShippingMethodInstance($this)) {
            return false;
        }

        return ($_GET['wc-ajax'] === 'update_order_review' && ! empty($_POST['post_data']))
            || $_GET['wc-ajax'] === 'checkout';
    }
}