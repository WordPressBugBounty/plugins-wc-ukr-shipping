<?php

namespace kirillbdev\WCUkrShipping\DB;

if ( ! defined('ABSPATH')) {
    exit;
}

class OptionsRepository
{
    /**
     * @param string $key
     * @return mixed|null
     */
    public static function getOption($key)
    {
        $defaults = [
            'wc_ukr_shipping_np_method_title' => 'Новая почта',
            'wc_ukr_shipping_np_block_title' => 'Укажите адрес доставки',
            'wc_ukr_shipping_np_placeholder_area' => 'Выберите область',
            'wc_ukr_shipping_np_placeholder_city' => 'Выберите город',
            'wc_ukr_shipping_np_placeholder_warehouse' => 'Выберите отделение',
            'wc_ukr_shipping_np_address_title' => 'Нужна адресная доставка',
            'wc_ukr_shipping_np_address_placeholder' => 'Введите адрес',
            'wc_ukr_shipping_np_not_found_text' => 'Ничего не найдено',
            'wc_ukr_shipping_np_block_pos' => 'billing',
            'wc_ukr_shipping_np_save_warehouse' => 1,
            'wc_ukr_shipping_np_translates_type' => WCUS_TRANSLATE_TYPE_PLUGIN,
            'wc_ukr_shipping_np_new_ui' => 1,
            'wcus_checkout_new_ui' => 1,
            'wc_ukr_shipping_np_ttn_payer_default' => 'Sender',
            'wcus_np_payment_method_default' => 'Cash',
            'wcus_cod_payment_id' => 'cod',
            'wcus_ttn_width_default' => 10,
            'wcus_ttn_height_default' => 10,
            'wcus_ttn_length_default' => 10,
            'wc_ukr_shipping_np_price_type' => 'fixed',
            'wc_ukr_shipping_np_price' => 50,
            'wc_ukr_shipping_np_cargo_type' => 'Cargo',
            'wcus_inject_additional_fields' => 0,
            'wcus_cost_view_only' => 0,
            'wcus_combine_poshtomats' => 0,

            // Ukrposhta
            'wcus_ukrposhta_service_type' => 'ukrposhta_standard',
            'wcus_ukrposhta_cost_view_only' => 0,
            'wcus_ukrposhta_cod_payment_active' => 0,
            'wcus_ukrposhta_price_type' => 'fixed',
            'wcus_ukrposhta_price' => 30,
            'wcus_ukrposhta_dd_provider' => 'none',
            'wc_ukr_shipping_np_address_api_ui' => 1,
            'wcus_sp_auto_tracking' => 1,
            'wcus_ukrposhta_ttn_default_payer' => 'recipient',
            'wcus_ukrposhta_on_fail_receive' => 'return',
            'wcus_ukrposhta_check_on_delivery' => 1,
            'wcus_ukrposhta_sms_notification' => 0,
            'wcus_ukrposhta_cod_payer' => 'recipient',

            // Nova Post
            'wcus_nova_post_cost_view_only' => 0,
            'wcus_nova_post_fixed_cost' => 0,

            // Rozetka delivery
            'wcus_rozetka_fixed_cost' => 0,
            'wcus_rozetka_cost_view_only' => 0,
        ];

        return get_option($key, isset($defaults[$key]) ? $defaults[$key] : null);
    }

    public function save($data)
    {
        foreach ($data['wc_ukr_shipping'] as $key => $value) {
            if (is_array($value)) {
                update_option('wc_ukr_shipping_' . $key, json_encode($value));
            } else {
                update_option('wc_ukr_shipping_' . $key, sanitize_text_field($value));
            }
        }

        foreach ($data['wcus'] as $key => $value) {
            if (is_array($value)) {
                update_option('wcus_' . $key, json_encode($value));
            } else {
                update_option('wcus_' . $key, sanitize_text_field($value));
            }
        }

        // Flush WooCommerce Shipping Cache
        delete_option('_transient_shipping-transient-version');
    }

    public function deleteAll()
    {
        delete_option('_transient_shipping-transient-version');
    }
}