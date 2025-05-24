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
                        <?php
                            $plans = [
                                'Basic' => [
                                    'id' => 'basic',
                                    'price' => 49,
                                    'features' => [
                                        __('Calculation of delivery via courier', 'wc-ukr-shipping-i18n'),
                                        __('Calculation of delivery depending on the order amount', 'wc-ukr-shipping-i18n'),
                                        __('Creating invoices', 'wc-ukr-shipping-i18n'),
                                        __('Printing invoices', 'wc-ukr-shipping-i18n'),
                                        __('Tracking invoices', 'wc-ukr-shipping-i18n'),
                                        __('Bulk generation of invoices in one click', 'wc-ukr-shipping-i18n'),
                                    ]
                                ],
                            ];
                        ?>

                        <?php foreach ($plans as $planName => $plan) { ?>
                            <div class="wcus-subscription wcus-subscription--popular wcus-upgrade__subscription">
                                <div class="wcus-subscription__name"><?php echo esc_html($planName); ?></div>
                                <div class="wcus-subscription__price">
                                    <span class="wcus-subscription__price-number">$<?php echo esc_html($plan['price']); ?></span>
                                    <span class="wcus-subscription__price-suffix">/ <?php esc_html_e('year', 'wc-ukr-shipping-i18n'); ?></span>
                                </div>
                                <div class="wcus-subscription__features">
                                    <?php foreach ($plan['features'] as $index => $feature) {  ?>
                                        <div class="wcus-subscription__feature">
                                            <?php echo wc_ukr_shipping_import_svg('check.svg'); ?>
                                            <span><?php echo esc_html($feature); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <a href="#" class="wcus-btn wcus-btn--primary wcus-btn--md j-wcus-upgrade" data-plan="<?php echo esc_attr($plan['id']); ?>">
                                    <?php esc_html_e('Switch to', 'wc-ukr-shipping-i18n'); ?>
                                    <?php echo esc_html($planName); ?>
                                </a>
                            </div>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?= \kirillbdev\WCUSCore\Foundation\View::render('partial/pro_promotion', ['hidePromo' => true]); ?>

</div>