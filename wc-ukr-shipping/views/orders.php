<div class="wcus-settings wcus-settings--full">
    <div class="wcus-settings__header">
        <h1 class="wcus-settings__title"><?= __('Settings', 'wc-ukr-shipping-i18n'); ?></h1>
        <div class="wcus-settings__head-buttons">
            <a target="_blank" href="https://kirillbdev.pro/docs/wcus-smarty-parcel-labels-batch/" class="wcus-btn wcus-btn--docs wcus-btn--md wcus-settings__docs">
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
        <div id="wcus-order-list"></div>
    </div>
</div>

<script>
    (function ($) {
        $(function () {
            window.WcusOrders.init({
                autoTracking: <?php echo esc_js((int)wc_ukr_shipping_get_option('wcus_sp_auto_tracking')); ?>
            });
        });
    })(jQuery);
</script>