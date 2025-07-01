<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Factories\Rates\Ukrposhta\UkrposhtaCheckoutOrderFactory;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\CalculationService;

if ( ! defined('ABSPATH')) {
    exit;
}

class NovaPostShipping extends \WC_Shipping_Method
{
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = WCUS_SHIPPING_METHOD_NOVA_POST;
        $this->method_title = __('Nova Post', 'wc-ukr-shipping-i18n');
        $this->method_description = 'Nova Post by WC Ukraine Shipping';

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

    function init(): void
    {
        $this->init_settings();
        $this->init_form_fields();

        $this->title = __('Nova Post', 'wc-ukr-shipping-i18n');

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields(): void
    {
        $this->instance_form_fields = [
            'title' => [
                'title' => __('Name', 'woocommerce' ),
                'type' => 'text',
                'description' => '',
                'default' => __('Nova Post', 'wc-ukr-shipping-i18n'),
            ],
            'manage_options' => [
                'type' => 'wcus_message',
                'text' => __('You can manage shipping method options at', 'wc-ukr-shipping-i18n'),
                'link' => home_url('wp-admin/admin.php?page=wc_ukr_shipping_options&section=nova_post'),
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
        $this->add_rate([
            'label' => $this->title,
            'cost' => (float)wc_ukr_shipping_get_option('wcus_nova_post_fixed_cost'),
            'package' => $package,
        ]);
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
