<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>

<div class="wcus-pro-features">
    <div class="wcus-card">
        <div class="wcus-card__content">
            <div class="wcus-card__title wcus-pro-features__title"><?= __('Get more features from SmartyParcel Advanced', 'wc-ukr-shipping-i18n'); ?></div>
            <div class="wcus-pro-features__list">
                <div class="wcus-pro-features__feature">
                    <?= __('Higher shipments limit', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Automatic calculation of shipping costs.', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Shipping calculation based on order total', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Ability to customize separated shipping costs for address shipping', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Possibility of mass generation of TTN in one click', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('TTN Tracking', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Constructor of business rules automation', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Premium support', 'wc-ukr-shipping-i18n'); ?>
                </div>
            </div>

            <a target="_blank"
               href="https://kirillbdev.pro/wc-ukr-shipping-pro/?utm_source=plugin"
               class="wcus-btn wcus-btn--primary wcus-btn--md wcus-btn--block wcus-pro-features__btn">
                <?= wc_ukr_shipping_import_svg('star.svg'); ?>
                <?= __('Switch to Advanced plan', 'wc-ukr-shipping-i18n'); ?>
            </a>

        </div>
    </div>
</div>
