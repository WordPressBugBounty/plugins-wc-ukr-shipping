<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;

if ( ! defined('ABSPATH')) {
    exit;
}

class RozetkaDeliveryShipping extends \WC_Shipping_Method
{
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = WCUS_SHIPPING_METHOD_ROZETKA;
        $this->method_title = __('Rozetka Delivery', 'wc-ukr-shipping-i18n');
        $this->method_description = 'Rozetka Delivery by WC Ukraine Shipping';

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

        $this->title = $this->get_option('title');

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
                'default' => __('Rozetka Delivery', 'wc-ukr-shipping-i18n'),
            ],
            'manage_options' => [
                'type' => 'wcus_message',
                'text' => __('You can manage shipping method options at', 'wc-ukr-shipping-i18n'),
                'link' => home_url('wp-admin/admin.php?page=wc_ukr_shipping_options&section=rozetka'),
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
            'cost' => (float)wc_ukr_shipping_get_option('wcus_rozetka_fixed_cost'),
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
