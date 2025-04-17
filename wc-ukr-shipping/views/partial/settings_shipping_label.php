<div id="wcus-pane-shipping-label" class="wcus-tab-pane">
    <div class="wcus-form-group">
        <div class="wcus-form-group__tooltip">
            <?php esc_html_e('The plugin must be connected to the Smarty Parcel service to use the functionality of creating a TTN', 'wc-ukr-shipping-i18n'); ?>
        </div>
    </div>

    <div class="wcus-form-group">
        <label for="wcus_nova_poshta_default_carrier"><?php esc_html_e('Default carrier account', 'wc-ukr-shipping-i18n'); ?></label>
        <select id="wcus_nova_poshta_default_carrier" name="wcus[nova_poshta_default_carrier]" class="wcus-form-control">
            <?php foreach ($carrierAccounts as $account) { ?>
                <option value="<?php echo esc_attr($account['id']); ?>" <?php echo get_option('wcus_nova_poshta_default_carrier') === $account['id'] ? 'selected' : ''; ?>>
                    <?php echo esc_html($account['name']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div id="wcus-settings-ttn-sender"></div>

    <?php
        $payer = wc_ukr_shipping_get_option('wc_ukr_shipping_np_ttn_payer_default');
    ?>
    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_ttn_payer_default"><?php esc_html_e('Delivery payer', 'wc-ukr-shipping-i18n'); ?></label>
        <select id="wc_ukr_shipping_np_ttn_payer_default"
                name="wc_ukr_shipping[np_ttn_payer_default]"
                class="wcus-form-control">
            <option value="Sender" <?php echo $payer === 'Sender' ? 'selected' : ''; ?>><?php esc_html_e('Sender', 'wc-ukr-shipping-i18n'); ?></option>
            <option value="Recipient" <?php echo $payer === 'Recipient' ? 'selected' : ''; ?>><?php esc_html_e('Recipient', 'wc-ukr-shipping-i18n'); ?></option>
        </select>
    </div>

    <?php
        $paymentMethod = wc_ukr_shipping_get_option('wcus_np_payment_method_default');
    ?>
    <div class="wcus-form-group">
        <label for="wcus_np_payment_method_default"><?php esc_html_e('Payment method', 'wc-ukr-shipping-i18n'); ?></label>
        <select id="wcus_np_payment_method_default"
                name="wcus[np_payment_method_default]"
                class="wcus-form-control">
            <option value="Cash" <?php echo $paymentMethod === 'Cash' ? 'selected' : ''; ?>><?php esc_html_e('Cash', 'wc-ukr-shipping-i18n'); ?></option>
            <option value="NonCash" <?php echo $paymentMethod === 'NonCash' ? 'selected' : ''; ?>><?php esc_html_e('NonCash', 'wc-ukr-shipping-i18n'); ?></option>
        </select>
    </div>

    <div class="wcus-control-group">
        <div class="wcus-control-group__title"><?php esc_html_e('Default dimensions', 'wc-ukr-shipping-i18n'); ?></div>
        <div class="wcus-control-group__content">
            <div class="wcus-row">
                <div class="wcus-col-md-2">
                    <div class="wcus-form-group">
                        <label for="wcus_ttn_width_default"><?= __('Width (cm)', 'wc-ukr-shipping-i18n'); ?></label>
                        <input type="text"
                               id="wcus_ttn_width_default"
                               name="wcus[ttn_width_default]"
                               class="wcus-form-control"
                               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wcus_ttn_width_default')); ?>">
                    </div>
                </div>
                <div class="wcus-col-md-2">
                    <div class="wcus-form-group">
                        <label for="wcus_ttn_height_default"><?= __('Height (cm)', 'wc-ukr-shipping-i18n'); ?></label>
                        <input type="text"
                               id="wcus_ttn_height_default"
                               name="wcus[ttn_height_default]"
                               class="wcus-form-control"
                               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wcus_ttn_height_default')); ?>">
                    </div>
                </div>
                <div class="wcus-col-md-2">
                    <div class="wcus-form-group">
                        <label for="wcus_ttn_length_default"><?= __('Length (cm)', 'wc-ukr-shipping-i18n'); ?></label>
                        <input type="text"
                               id="wcus_ttn_length_default"
                               name="wcus[ttn_length_default]"
                               class="wcus-form-control"
                               value="<?php echo esc_attr(wc_ukr_shipping_get_option('wcus_ttn_length_default')); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wcus-control-group">
        <div class="wcus-control-group__title"><?php esc_html_e('COD', 'wc-ukr-shipping-i18n'); ?></div>
        <div class="wcus-control-group__content">
            <div class="wcus-form-group">
                <label for="wcus_cod_payment_id"><?php esc_html_e('COD method', 'wc-ukr-shipping-i18n'); ?></label>
                <select id="wcus_cod_payment_id" name="wcus[cod_payment_id]" class="wcus-form-control">
                    <?php foreach ($payment_methods as $method) { ?>
                        <option value="<?php echo esc_attr($method['id']); ?>" <?php echo $cod_payment_id === $method['id'] ? 'selected' : ''; ?>><?php echo esc_html($method['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>

    <?php
        \kirillbdev\WCUkrShipping\Helpers\HtmlHelper::switcherField(
            'wcus[ttn_pay_control_default]',
            __('Payment control', 'wc-ukr-shipping-i18n'),
            $payment_control_default === 1
        );
    ?>

    <?php
        \kirillbdev\WCUkrShipping\Helpers\HtmlHelper::switcherField(
            'wcus[sp_auto_tracking]',
            __('Automatically add shipments to tracking', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_sp_auto_tracking') === 1
        );
    ?>

</div>