<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }
?>

<div class="wcus-layout">

    <div class="wcus-settings-layout">
        <div class="wcus-settings">
            <div class="wcus-settings__content">

                <?php if ($quotaReached) { ?>
                    <div class="wcus-message wcus-message--error wcus-mb-4" style="font-size: 16px;">
                        <?php esc_html_e('Unfortunately, you have exhausted your trial quota on the Free plan. To continue creating invoices, please upgrade to the Advanced plan.', 'wc-ukr-shipping-i18n'); ?>
                    </div>
                <?php } ?>

                <div id="page-wcus-upgrade" class="wcus-upgrade">
                    <div class="wcus-upgrade__icon wcus-mb-2">
                        <img src="<?php echo esc_attr(WC_UKR_SHIPPING_PLUGIN_URL . '/image/smarty-parcel.jpg'); ?>" />
                    </div>
                    <div class="wcus-upgrade__title wcus-mb-5"><?php esc_html_e('Unlock powerful logistics automation tools', 'wc-ukr-shipping-i18n'); ?></div>

                    <div class="wcus-upgrade__subscriptions">

                        <div class="wcus-subscription wcus-upgrade__subscription">
                            <div class="wcus-subscription__name">Free</div>
                            <div class="wcus-subscription__price">
                                <div class="wcus-subscription__price-number">
                                    <?php esc_html_e('Free', 'wc-ukr-shipping-i18n'); ?>
                                </div>
                            </div>
                            <div class="wcus-subscription__description">
                                <?php esc_html_e('Basic set of functions for logistics automation.', 'wc-ukr-shipping-i18n'); ?>
                            </div>
                            <?php
                                $features = [
                                    __('Selecting a delivery department', 'wc-ukr-shipping-i18n'),
                                    __('Address delivery', 'wc-ukr-shipping-i18n'),
                                    __('Fixed cost calculation', 'wc-ukr-shipping-i18n'),
                                    __('25 invoices at a time (creation / tracking)', 'wc-ukr-shipping-i18n'),
                                ];
                            ?>
                            <div class="wcus-subscription__features">
                                <?php foreach ($features as $index => $feature) { ?>
                                    <div class="wcus-subscription__feature">
                                        <?php echo wc_ukr_shipping_import_svg('check.svg'); ?>
                                        <span>
                                            <?php if ($index === count($features) - 1) { ?>
                                                <strong><?php echo esc_html($feature); ?></strong>
                                            <?php } else { ?>
                                                <?php echo esc_html($feature); ?>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="wcus-subscription wcus-subscription--popular wcus-upgrade__subscription">
                            <div class="wcus-subscription__name">Advanced</div>
                            <div class="wcus-subscription__price">
                                <span class="wcus-subscription__price-number">4$</span>
                                <span class="wcus-subscription__price-suffix">/ <?php esc_html_e('month', 'wc-ukr-shipping-i18n'); ?></span>
                            </div>
                            <div class="wcus-subscription__options wcus-mb-2">
                                <label for="wcus-qouta" style="display: block; margin-bottom: 4px;"><?php esc_html_e('Select monthly turnover', 'wc-ukr-shipping-i18n'); ?></label>
                                <select id="wcus-qouta" class="wcus-form-control">
                                    <option value="advanced_50" data-price="4">50 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_200" data-price="9">200 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_500" data-price="13">500 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_1k" data-price="19">1000 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_2k" data-price="29">2000 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_5k" data-price="39">5000 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                    <option value="advanced_10k" data-price="59">10000 <?php esc_html_e('parcels / month', 'wc-ukr-shipping-i18n'); ?></option>
                                </select>
                                <div class="wcus-subscription__overage">$0.07 <?php esc_html_e('per extra parcel', 'wc-ukr-shipping-i18n'); ?></div>
                            </div>
                            <?php
                                $features = [
                                    __('Unlimited domains', 'wc-ukr-shipping-i18n'),
                                    __('Calculation of delivery via courier', 'wc-ukr-shipping-i18n'),
                                    __('Calculation of delivery depending on the order amount', 'wc-ukr-shipping-i18n'),
                                    __('Creating invoices', 'wc-ukr-shipping-i18n'),
                                    __('Printing invoices', 'wc-ukr-shipping-i18n'),
                                    __('Tracking invoices', 'wc-ukr-shipping-i18n'),
                                    __('Bulk generation of invoices in one click', 'wc-ukr-shipping-i18n'),
                                ];
                            ?>
                            <div class="wcus-subscription__features">
                                <?php foreach ($features as $index => $feature) {  ?>
                                    <div class="wcus-subscription__feature">
                                        <?php echo wc_ukr_shipping_import_svg('check.svg'); ?>
                                        <span>
                                            <?php if ($index === 0) { ?>
                                                <strong> <?php echo esc_html($feature); ?></strong>
                                            <?php } else { ?>
                                                <?php echo esc_html($feature); ?>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                            <a href="#" id="wcus-btn-upgrade" class="wcus-btn wcus-btn--primary wcus-btn--md">
                                <?php esc_html_e('Switch to Advanced plan', 'wc-ukr-shipping-i18n'); ?>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/pro_promotion', ['hidePromo' => true]); ?>

</div>