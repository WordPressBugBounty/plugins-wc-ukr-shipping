<?php
if ( ! defined('ABSPATH')) {
    exit;
}
?>

<div class="wcus-pro-features">
    <div class="wcus-card wcus-mb-2">
        <div class="wcus-card__header">
            <div class="wcus-card-icon"><?php echo wc_ukr_shipping_import_svg('help.svg') ?></div>
            <div class="wcus-card__title wcus-pro-features__title">
                <?php esc_html_e('Need help?', 'wc-ukr-shipping'); ?>
            </div>
        </div>
        <div class="wcus-card__content">
            <a target="_blank"
               href="https://smartyparcel.com/docs/knowledge-base-woocommerce/"
               class="wcus-btn wcus-btn--docs wcus-btn--md wcus-btn--block wcus-mb-1">
                <?php echo wc_ukr_shipping_import_svg('docs.svg'); ?>
                <?php esc_html_e('Documentation', 'wc-ukr-shipping'); ?>
            </a>
        </div>
    </div>

    <?php if (!isset($hidePromo)) { ?>
        <div class="wcus-card">
            <div class="wcus-card__content">
                <div class="wcus-pro-features__header">
                    <div class="wcus-pro-features__logo">
                        <img src="<?php echo esc_attr(WC_UKR_SHIPPING_PLUGIN_URL . '/image/smarty-parcel.jpg'); ?>" />
                    </div>
                    <div class="wcus-card__title wcus-pro-features__title"><?php esc_html_e('Unlock more features with paid plans', 'wc-ukr-shipping'); ?></div>
                </div>
                <div class="wcus-pro-features__list">
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('More shipments limit', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('More own carrier accounts', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Carrier live-rates', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Batch labels generation', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Batch labels printing', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Dynamic shipping rules', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Extended parcels analytics', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('SMS notifications', 'wc-ukr-shipping'); ?>
                    </div>
                    <div class="wcus-pro-features__feature">
                        <?php esc_html_e('Premium support', 'wc-ukr-shipping'); ?>
                    </div>
                </div>

                <a href="<?php echo esc_url(admin_url('admin.php?page=wcus_smarty_parcel#/upgrade')); ?>"
                   class="wcus-btn wcus-btn--md wcus-btn--block wcus-pro-features__btn">
                    <?php echo wc_ukr_shipping_import_svg('star.svg'); ?>
                    <?php esc_html_e('Switch to paid plan', 'wc-ukr-shipping'); ?>
                </a>

            </div>
        </div>
    <?php } ?>
</div>
