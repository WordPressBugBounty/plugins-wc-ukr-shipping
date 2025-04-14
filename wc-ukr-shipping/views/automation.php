<?php
    use kirillbdev\WCUkrShipping\Helpers\HtmlHelper;
?>

<?php if (isset($successMsg)) { ?>
    <div id="wcus-automation-success" class="notice inline notice-success notice-alt" style="padding-top: 10px; padding-bottom: 10px;">
        <?= $successMsg; ?>
    </div>
<?php } ?>

<form id="wcus-automation-rule-form" method="POST" action="#">
    <div class="wcus-settings wcus-settings--full">
        <div class="wcus-settings__header">
            <h1 class="wcus-settings__title"><?php esc_html_e('Rule constructor', 'wc-ukr-shipping-i18n'); ?></h1>
            <div class="wcus-settings__head-buttons">
                <a target="_blank" href="https://kirillbdev.pro/docs/wcus-automation-rule-builder/" class="wcus-btn wcus-btn--docs wcus-btn--md wcus-settings__docs">
                    <?= wc_ukr_shipping_import_svg('docs.svg'); ?>
                    <?= __('Documentation', 'wc-ukr-shipping-i18n'); ?>
                </a>
                <button type="submit" class="wcus-settings__submit wcus-btn wcus-btn--primary wcus-btn--md">
                    <?= __('Save', 'wc-ukr-shipping-i18n'); ?>
                </button>
            </div>
        </div>
        <div class="wcus-settings__content">
            <input type="hidden" name="rule_id" value="<?= $model !== null ? $model->id : 0; ?>" />
            <?php
            HtmlHelper::textField(
                'rule_name',
                __('Name', 'wc-ukr-shipping-i18n'),
                $model->name ?? ''
            );

            HtmlHelper::switcherField(
                'active',
                __('Active', 'wc-ukr-shipping-i18n'),
                $model !== null ? (bool)$model->active : true
            );
            ?>
            <div id="wcus-automation-app"></div>
        </div>
    </div>
</form>
<script>
    (function ($) {
        $(function () {
            <?php if ($model !== null) { ?>
                window.WcusAutomation.init({
                    event: {
                        type: '<?= $model->event_name ?>',
                        params: <?= $model->event_data; ?>
                    },
                    actions: <?= json_encode($model->actions); ?>
                });
            <?php } else { ?>
                window.WcusAutomation.init();
            <?php } ?>
        });
    })(jQuery);
</script>