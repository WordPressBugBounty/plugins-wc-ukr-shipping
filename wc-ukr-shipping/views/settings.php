<?php
  if ( ! defined('ABSPATH')) {
      exit;
  }
?>

<div class="wcus-layout">

    <div class="wcus-settings-layout">

        <div id="wc-ukr-shipping-settings" class="wcus-settings wcus-settings--full">
            <div class="wcus-settings__header">
                <div class="wcus-card-icon"><?php echo wc_ukr_shipping_import_svg('settings.svg') ?></div>
                <h1 class="wcus-settings__title">
                    <?= __('Settings', 'wc-ukr-shipping-i18n'); ?>
                </h1>
                <div class="wcus-settings__head-buttons">
                    <button type="submit" form="wc-ukr-shipping-settings-form" class="wcus-settings__submit wcus-btn wcus-btn--primary wcus-btn--md">
                        <?= __('Save', 'wc-ukr-shipping-i18n'); ?>
                    </button>
                </div>
                <div id="wcus-settings-success-msg" class="wcus-settings__success wcus-message wcus-message--success"></div>
            </div>
            <div class="wcus-settings__content">
                <form id="wc-ukr-shipping-settings-form" action="/" method="POST">
                    <ul class="wcus-tabs">
                        <li data-pane="wcus-pane-general" class="active"><?php esc_html_e('General', 'wc-ukr-shipping-i18n'); ?></li>
                        <li data-pane="wcus-pane-shipping"><?php esc_html_e('Shipping', 'wc-ukr-shipping-i18n'); ?></li>
                        <li data-pane="wcus-pane-translates"><?php esc_html_e('Translates', 'wc-ukr-shipping-i18n'); ?></li>
                        <li data-pane="wcus-pane-shipping-label"><?php esc_html_e('Shipping Label', 'wc-ukr-shipping-i18n'); ?></li>
                    </ul>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_general'); ?>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_shipping'); ?>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_translates'); ?>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_shipping_label', [
                        'payment_methods' => $payment_methods,
                        'cod_payment_id' => $cod_payment_id,
                        'payment_control_default' => $payment_control_default,
                        'carrierAccounts' => $carrierAccounts,
                    ]); ?>
                </form>
            </div>
        </div>

    </div>

    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/pro_promotion'); ?>

</div>
