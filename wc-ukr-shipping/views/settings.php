<?php
  if ( ! defined('ABSPATH')) {
      exit;
  }
?>

<div class="wcus-layout">

    <div class="wcus-settings-layout">

        <div id="wc-ukr-shipping-settings" class="wcus-settings">
            <div class="wcus-settings__header">
                <h1 class="wcus-settings__title"><?= __('Settings', 'wc-ukr-shipping-i18n'); ?></h1>
                <div class="wcus-settings__head-buttons">
                    <a target="_blank" href="https://kirillbdev.pro/docs/wcus-base-setup/" class="wcus-btn wcus-btn--docs wcus-btn--md wcus-settings__docs">
                        <?= wc_ukr_shipping_import_svg('docs.svg'); ?>
                        <?= __('Documentation', 'wc-ukr-shipping-i18n'); ?>
                    </a>
                    <button type="submit" form="wc-ukr-shipping-settings-form" class="wcus-settings__submit wcus-btn wcus-btn--primary wcus-btn--md">
                        <?= __('Save', 'wc-ukr-shipping-i18n'); ?>
                    </button>
                </div>
                <div id="wcus-settings-success-msg" class="wcus-settings__success wcus-message wcus-message--success"></div>
            </div>
            <div class="wcus-settings__content">
                <form id="wc-ukr-shipping-settings-form" action="/" method="POST">
                    <ul class="wcus-tabs">
                        <li data-pane="wcus-pane-general" class="active"><?= __('General', 'wc-ukr-shipping-i18n'); ?></li>
                        <li data-pane="wcus-pane-shipping"><?= __('Shipping', 'wc-ukr-shipping-i18n'); ?></li>
                        <li data-pane="wcus-pane-translates"><?= __('Translates', 'wc-ukr-shipping-i18n'); ?></li>
                    </ul>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_general'); ?>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_shipping'); ?>
                    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/settings_translates'); ?>
                </form>
            </div>
        </div>

        <div id="wcus-danger-zone" class="wcus-settings">
            <div class="wcus-settings__header">
                <h1 class="wcus-settings__title">
                    <?php esc_html_e('Danger zone', 'wc-ukr-shipping-i18n'); ?>
                </h1>
            </div>
            <div class="wcus-settings__content">
                <div class="wcus-form-group" style="margin-bottom: 0;">
                    <button id="rerun_migrations" class="wcus-btn wcus-btn--docs wcus-btn--sm"><?php esc_html_e('Re-run migrations', 'wc-ukr-shipping-i18n'); ?></button>
                    <div class="wcus-form-group__tooltip">
                        <?php esc_html_e('Be careful when performing this action. Be sure to refresh the list of warehouses after this action will be completed.', 'wc-ukr-shipping-i18n'); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/pro_promotion'); ?>

</div>
