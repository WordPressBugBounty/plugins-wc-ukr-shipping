<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }
?>

<div class="wcus-settings wcus-settings--full wcus-top-panel">
    <div class="wcus-settings__content">
        <div class="wcus-top-panel__nav">
            <a href="<?php echo esc_attr(admin_url('admin.php?page=wc_ukr_shipping_options')); ?>"
               class="wcus-top-panel__nav-link <?php echo $section === 'general' ? 'wcus-top-panel__nav-link--active' : ''; ?>">
                <?php esc_html_e('General settings', 'wc-ukr-shipping-i18n'); ?>
            </a>
            <a href="<?php echo esc_attr(admin_url('admin.php?page=wc_ukr_shipping_options&section=nova_poshta')); ?>"
               class="wcus-top-panel__nav-link wcus-top-panel__nav-link--nova-poshta <?php echo $section === 'nova_poshta' ? 'wcus-top-panel__nav-link--active' : ''; ?>">
                <?php esc_html_e('Nova Poshta', 'wc-ukr-shipping-i18n'); ?>
            </a>
            <a href="<?php echo esc_attr(admin_url('admin.php?page=wc_ukr_shipping_options&section=ukrposhta')); ?>"
               class="wcus-top-panel__nav-link wcus-top-panel__nav-link--ukrposhta <?php echo $section === 'ukrposhta' ? 'wcus-top-panel__nav-link--active' : ''; ?>">
                <?php esc_html_e('Ukrposhta', 'wc-ukr-shipping-i18n'); ?>
            </a>
        </div>
    </div>
</div>
