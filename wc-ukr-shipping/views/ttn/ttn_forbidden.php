<?php
if ( ! defined('ABSPATH')) {
    exit;
}

use kirillbdev\WCUkrShipping\Helpers\SmartyParcelHelper;

$return_to = admin_url('admin.php?page=wc_ukr_shipping_ttn&order_id=' . (int)($_GET['order_id'] ?? 0));
?>

<div class="wcus-layout">

    <div class="wcus-settings-layout" style="width: 90%;">
        <div id="wcus-smarty-parcel-settings" class="wcus-settings">
            <div class="wcus-settings__header">
                <h1 class="wcus-settings__title"><?php esc_html_e('New TTN', 'wc-ukr-shipping'); ?></h1>
            </div>
            <div class="wcus-settings__content">
                <p style="font-size: 14px; margin-top: 0;">
                    <?php echo esc_html(SmartyParcelHelper::getAccountRequiredMessage()); ?>
                </p>
                <?php if (SmartyParcelHelper::currentUserCanConnect()) { ?>
                    <a href="<?php echo esc_url(SmartyParcelHelper::getConnectUrl($return_to)); ?>"
                       class="wcus-btn wcus-btn--primary wcus-btn--md">
                        <?php esc_html_e('Connect SmartyParcel account', 'wc-ukr-shipping'); ?>
                    </a>
                <?php } else { ?>
                    <p style="font-size: 14px; margin-bottom: 0;">
                        <?php esc_html_e('Ask the site administrator to connect the store to a SmartyParcel account.', 'wc-ukr-shipping'); ?>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>

</div>
