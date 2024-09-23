<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>

<div class="wcus-pro-features">
    <div class="wcus-card">
        <div class="wcus-card__content">
            <div class="wcus-card__title wcus-pro-features__title"><?= __('Get more features from our PRO version', 'wc-ukr-shipping-i18n'); ?></div>
            <div class="wcus-pro-features__list">
                <div class="wcus-pro-features__feature">
                    <?= __('Full address shipping integration (using Nova Poshta address API)', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Automatic calculation of shipping costs. Supporting W2W, W2D and COD delivery calculation', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Shipping calculation based on order total', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Ability to customize separated shipping costs for address shipping', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Ability to generate TTN. Support all of types: W2W, W2D, D2W, D2D', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Possibility of mass generation of TTN in one click', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Print TTN of all types: A4 (1 copy), A4 (2 copies), sticker 85x85, sticker 100х100 (zebra)', 'wc-ukr-shipping-i18n'); ?>
                </div>
                <div class="wcus-pro-features__feature">
                    <?= __('Automatic email notifications after TTN creation', 'wc-ukr-shipping-i18n'); ?>
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
                <?= __('Buy PRO version', 'wc-ukr-shipping-i18n'); ?>
            </a>
            <a target="_blank"
               href="https://kirillbdev.pro/wc-ukr-shipping-pro/#request-demo"
               class="wcus-btn wcus-btn--docs wcus-btn--md wcus-btn--block wcus-pro-features__btn">
                <?= __('Request demo', 'wc-ukr-shipping-i18n'); ?>
            </a>

        </div>
    </div>
</div>
