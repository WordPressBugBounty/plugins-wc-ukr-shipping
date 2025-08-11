<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-translates" class="wcus-tab-pane">
    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_method_title"><?php esc_html_e('Shipping method name', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_method_title"
               name="wc_ukr_shipping[np_method_title]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_method_title')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_block_title"><?php esc_html_e('Shipping block title', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_block_title"
               name="wc_ukr_shipping[np_block_title]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_block_title')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_placeholder_area"><?php esc_html_e('Placeholder of select area field', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_placeholder_area"
               name="wc_ukr_shipping[np_placeholder_area]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_placeholder_area')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_placeholder_city"><?php esc_html_e('Placeholder of select city field', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_placeholder_city"
               name="wc_ukr_shipping[np_placeholder_city]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_placeholder_city')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_placeholder_warehouse"><?php esc_html_e('Placeholder of select warehouse field', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_placeholder_warehouse"
               name="wc_ukr_shipping[np_placeholder_warehouse]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_placeholder_warehouse')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_address_title"><?php esc_html_e('Label of address selector', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_address_title"
               name="wc_ukr_shipping[np_address_title]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_title')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_address_placeholder"><?php esc_html_e('Placeholder of address field', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_address_placeholder"
               name="wc_ukr_shipping[np_address_placeholder]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_placeholder')); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_not_found_text"><?php esc_html_e('Empty result text', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="text" id="wc_ukr_shipping_np_not_found_text"
               name="wc_ukr_shipping[np_not_found_text]"
               class="wcus-form-control"
               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wc_ukr_shipping_np_not_found_text')); ?>">
    </div>
</div>