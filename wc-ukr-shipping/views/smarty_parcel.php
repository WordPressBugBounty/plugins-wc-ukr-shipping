<?php
  if ( ! defined('ABSPATH')) {
      exit;
  }
?>

<div class="wcus-layout">

    <div class="wcus-settings-layout">
        <div id="wcus-smarty-parcel-settings" class="wcus-settings">
            <div class="wcus-settings__header">
                <div class="wcus-card-icon"><?php echo wc_ukr_shipping_import_svg('truck.svg') ?></div>
                <h1 class="wcus-settings__title"><?php esc_html_e('Smarty Parcel', 'wc-ukr-shipping-i18n'); ?></h1>
            </div>
            <div class="wcus-settings__content">
                <div id="wcus-smarty-parcel-auth"></div>
            </div>
        </div>
    </div>

    <?php echo \kirillbdev\WCUSCore\Foundation\View::render('partial/pro_promotion'); ?>

</div>
