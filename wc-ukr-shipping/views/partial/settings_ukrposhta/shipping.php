<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-shipping" class="wcus-tab-pane active">

    <?php
        HtmlHelper::switcherField(
            'wcus[ukrposhta_cost_view_only]',
            __('Calculate shipping cost for view only', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_ukrposhta_cost_view_only')
        );

        HtmlHelper::switcherField(
            'wcus[ukrposhta_cod_payment_active]',
            __('Take COD into account when calculating shipping cost', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_ukrposhta_cod_payment_active')
        );
    ?>

    <div id="wcus-settings-ukrposhta-shipping-cost"></div>

    <div id="wcus-settings-ukrposhta-pudo"></div>

</div>
