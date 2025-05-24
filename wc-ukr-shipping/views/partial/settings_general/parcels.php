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

    <div class="wcus-form-group">
        <label for="wcus_ttn_description"><?= __('Default description', 'wc-ukr-shipping-i18n'); ?></label>
        <textarea id="wcus_ttn_description"
               name="wcus[ttn_description]"
               class="wcus-form-control" style="min-height: 70px;"><?php echo esc_html(wc_ukr_shipping_get_option('wcus_ttn_description')); ?></textarea>
    </div>

    <?php
        HtmlHelper::textField(
            'wcus[ttn_weight_default]',
            __('Default weight', 'wc-ukr-shipping-i18n'),
            wc_ukr_shipping_get_option('wcus_ttn_weight_default')
        );
    ?>

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

    <?php
        HtmlHelper::switcherField(
            'wcus[sp_auto_tracking]',
            __('Automatically add shipments to tracking', 'wc-ukr-shipping-i18n'),
            (int)wc_ukr_shipping_get_option('wcus_sp_auto_tracking') === 1
        );
    ?>

</div>
