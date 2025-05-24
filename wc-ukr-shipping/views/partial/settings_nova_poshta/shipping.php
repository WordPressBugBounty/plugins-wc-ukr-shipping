<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-shipping" class="wcus-tab-pane">

    <?php
        HtmlHelper::switcherField(
            'wcus[cost_view_only]',
            __('Calculate shipping cost for view only', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_cost_view_only')
        );

        HtmlHelper::switcherField(
            'wcus[cod_payment_active]',
            __('Take COD into account when calculating shipping cost', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_cod_payment_active')
        );
    ?>

    <div id="wcus-settings-nova-ppshta-shipping-cost"></div>

</div>
