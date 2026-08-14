<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use \kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<div id="wcus-pane-parcels" class="wcus-tab-pane active">

    <div class="wcus-message wcus-message--warning wcus-mb-2">
        <?php esc_html_e('The plugin must be connected to the Smarty Parcel service to use the functionality of creating a TTN', 'wc-ukr-shipping'); ?>
        <a href="https://smartyparcel.com/docs/wcus-smarty-parcel-connect/" target="_blank"><?php esc_html_e('Documentation', 'wc-ukr-shipping'); ?></a>
    </div>

    <?php
        HtmlHelper::selectField(
            'wcus[ukrposhta_ttn_default_payer]',
             __('Delivery payer', 'wc-ukr-shipping'),
            [
                'sender' => __('Sender', 'wc-ukr-shipping'),
                'recipient' => __('Recipient', 'wc-ukr-shipping'),
            ],
            wc_ukr_shipping_get_option('wcus_ukrposhta_ttn_default_payer')
        );

        HtmlHelper::selectField(
            'wcus[ukrposhta_on_fail_receive]',
            __('On fail receive', 'wc-ukr-shipping'),
            [
                'return' => __('Return', 'wc-ukr-shipping'),
                'process_as_refusal' => __('Process as refusal', 'wc-ukr-shipping'),
            ],
            wc_ukr_shipping_get_option('wcus_ukrposhta_on_fail_receive')
        );

        HtmlHelper::switcherField(
        'wcus[ukrposhta_check_on_delivery]',
            __('Check on delivery', 'wc-ukr-shipping'),
            (int)wc_ukr_shipping_get_option('wcus_ukrposhta_check_on_delivery') === 1
        );

        HtmlHelper::switcherField(
            'wcus[ukrposhta_sms_notification]',
            __('SMS notification', 'wc-ukr-shipping'),
            (int)wc_ukr_shipping_get_option('wcus_ukrposhta_sms_notification') === 1
        );

        HtmlHelper::selectField(
            'wcus[ukrposhta_cod_payer]',
            __('COD payer', 'wc-ukr-shipping'),
            [
                'sender' => __('Sender', 'wc-ukr-shipping'),
                'recipient' => __('Recipient', 'wc-ukr-shipping'),
            ],
            wc_ukr_shipping_get_option('wcus_ukrposhta_cod_payer')
        );
    ?>

</div>
