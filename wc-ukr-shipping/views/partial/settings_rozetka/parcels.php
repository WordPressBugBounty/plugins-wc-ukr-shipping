<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
    use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
?>

<div id="wcus-pane-shipment" class="wcus-tab-pane">

    <div class="wcus-form-group">
        <div class="wcus-form-group__tooltip">
            <?php esc_html_e('The plugin must be connected to the Smarty Parcel service to use the functionality of creating a TTN', 'wc-ukr-shipping-i18n'); ?>
        </div>
    </div>

    <?php
        $ukrPoshtaAccounts = [];
        foreach ($carrierAccounts as $account) {
            if (($account['carrier_slug'] ?? '') === 'rozetka_delivery') {
                $ukrPoshtaAccounts[$account['id']] = $account['name'];;
            }
        }

        HtmlHelper::selectField(
            'wcus[rozetka_default_carrier]',
            __('Default carrier account', 'wc-ukr-shipping-i18n'),
            $ukrPoshtaAccounts,
            wc_ukr_shipping_get_option('wcus_rozetka_default_carrier')
        );
    ?>

    <div class="wcus-control-group">
        <div class="wcus-control-group__title"><?php esc_html_e('Sender', 'wc-ukr-shipping-i18n'); ?></div>
        <div class="wcus-control-group__content">

            <?php
                $sender = WCUSHelper::safeGetJsonOption('wcus_rozetka_ttn_sender');

                HtmlHelper::textField(
                'wcus[rozetka_ttn_sender][name]',
                    __('Name', 'wc-ukr-shipping-i18n'),
                    $sender['name'] ?? ''
                );

                HtmlHelper::textField(
                    'wcus[rozetka_ttn_sender][phone]',
                    __('Phone', 'wc-ukr-shipping-i18n'),
                    $sender['phone'] ?? ''
                );

                HtmlHelper::textField(
                    'wcus[rozetka_ttn_sender][email]',
                    __('Email', 'wc-ukr-shipping-i18n'),
                    $sender['email'] ?? ''
                );
            ?>

            <div class="j-wcus-ship-address"
                 data-carrier="rozetka_delivery"
                 data-prefix="wcus[rozetka_ttn_sender]"
                 data-city="<?php echo esc_attr(json_encode($sender['city'])); ?>"
                 data-warehouse="<?php echo esc_attr(json_encode($sender['warehouse'])); ?>"></div>

        </div>
    </div>

</div>
