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

        $sender = json_decode(wc_ukr_shipping_get_option('wcus_ukrposhta_ttn_sender'), true);
        if (!$sender) {
            $sender = [];
        }

        HtmlHelper::textField(
            'wcus[ukrposhta_ttn_sender][first_name]',
            __('Sender first name', 'wc-ukr-shipping-i18n'),
            $sender['first_name'] ?? ''
        );

        HtmlHelper::textField(
            'wcus[ukrposhta_ttn_sender][last_name]',
            __('Sender last name', 'wc-ukr-shipping-i18n'),
            $sender['last_name'] ?? ''
        );

        HtmlHelper::textField(
            'wcus[ukrposhta_ttn_sender][middle_name]',
            __('Sender middle name', 'wc-ukr-shipping-i18n'),
            $sender['middle_name'] ?? ''
        );

        HtmlHelper::textField(
            'wcus[ukrposhta_ttn_sender][phone]',
            __('Sender phone', 'wc-ukr-shipping-i18n'),
            $sender['phone'] ?? ''
        );
    ?>

    <input type="hidden" name="wcus[ukrposhta_ttn_sender][type]" value="<?php echo esc_attr($sender['type'] ?? 'individual'); ?>">

</div>
