<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-shipping" class="wcus-tab-pane active">

    <?php
        HtmlHelper::switcherField(
            'wcus[nova_post_cost_view_only]',
            __('Calculate shipping cost for view only', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_nova_post_cost_view_only')
        );

        HtmlHelper::textField(
                'wcus[nova_post_api_key]',
            __('API Key', 'wc-ukr-shipping-i18n'),
            wc_ukr_shipping_get_option('wcus_nova_post_api_key'),
            __('API key is required to search for warehouses and PUDO across Europe', 'wc-ukr-shipping-i18n')
        );

        HtmlHelper::textField(
            'wcus[nova_post_fixed_cost]',
            __('Fixed shipping cost', 'wc-ukr-shipping-i18n'),
            wc_ukr_shipping_get_option('wcus_nova_post_fixed_cost'),
            __('Use 0 to disable shipping calculation', 'wc-ukr-shipping-i18n')
        );
    ?>

</div>
