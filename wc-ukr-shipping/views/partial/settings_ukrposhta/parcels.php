<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-parcels" class="wcus-tab-pane">

    <div class="wcus-form-group">
        <div class="wcus-form-group__tooltip">
            <?php esc_html_e('The plugin must be connected to the Smarty Parcel service to use the functionality of creating a TTN', 'wc-ukr-shipping-i18n'); ?>
        </div>
    </div>

    <?php
        $ukrPoshtaAccounts = [];
        foreach ($carrierAccounts as $account) {
            if (($account['carrier_slug'] ?? '') === 'ukrposhta') {
                $ukrPoshtaAccounts[$account['id']] = $account['name'];;
            }
        }

        HtmlHelper::selectField(
            'wcus[ukrposhta_default_carrier]',
            __('Default carrier account', 'wc-ukr-shipping-i18n'),
            $ukrPoshtaAccounts,
            wc_ukr_shipping_get_option('wcus_ukrposhta_default_carrier')
        );
    ?>

    <div id="wcus-ukrposhta-sender"></div>

</div>
