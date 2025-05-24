<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-checkout" class="wcus-tab-pane active">

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_lang"><?= __('Display language of cities and departments', 'wc-ukr-shipping-i18n'); ?></label>
        <select id="wc_ukr_shipping_np_lang"
                name="wc_ukr_shipping[np_lang]"
                class="wcus-form-control">
            <option value="ru" <?= get_option('wc_ukr_shipping_np_lang', 'uk') === 'ru' ? 'selected' : ''; ?>><?= __('Russian', 'wc-ukr-shipping-i18n'); ?></option>
            <option value="uk" <?= get_option('wc_ukr_shipping_np_lang', 'uk') === 'uk' ? 'selected' : ''; ?>><?= __('Ukrainian', 'wc-ukr-shipping-i18n'); ?></option>
        </select>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wc_ukr_shipping[address_shipping]" value="0">
            <input type="checkbox" name="wc_ukr_shipping[address_shipping]" value="1" <?= (int)get_option('wc_ukr_shipping_address_shipping', 1) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('Enable address shipping', 'wc-ukr-shipping-i18n'); ?></div>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wcus[show_poshtomats]" value="0">
            <input type="checkbox" name="wcus[show_poshtomats]" value="1" <?= (int)get_option('wcus_show_poshtomats', 1) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('Show poshtomats', 'wc-ukr-shipping-i18n'); ?></div>
    </div>

    <?php
        \kirillbdev\WCUkrShipping\Helpers\HtmlHelper::switcherField(
            'wc_ukr_shipping[np_address_api_ui]',
            __('Use Nova Poshta API for address shipping', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_api_ui')
        );
    ?>

    <div id="wcus-warehouse-loader"></div>

</div>
