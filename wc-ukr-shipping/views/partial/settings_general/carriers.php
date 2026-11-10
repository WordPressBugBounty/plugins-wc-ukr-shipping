<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    use kirillbdev\WCUkrShipping\Helpers\SmartyParcelHelper;

    /** @var array $carriers */

    $isAccountConnected = SmartyParcelHelper::isConnected();
    $accountsUrl = admin_url('admin.php?page=wcus_smarty_parcel#/carriers');
    $connectUrl = $isAccountConnected ? '' : SmartyParcelHelper::getConnectUrl($accountsUrl);
?>

<div class="wcus-message wcus-message--info wcus-mb-3">
    <div style="font-size: 14px; line-height: 1.4;">
        <div class="wcus-mb-1">
            <?php esc_html_e('Enable the carriers you want to integrate with your store.', 'wc-ukr-shipping'); ?>
        </div>
        <div class="wcus-mb-1">
            <a target="_blank" href="https://smartyparcel.com/supported-carriers/">
                <?php esc_html_e('List of supported carriers and features', 'wc-ukr-shipping'); ?>
            </a>
        </div>
        <div>
            <a target="_blank" href="https://smartyparcel.com/supported-pickup-points/">
                <?php esc_html_e('List of supported Pickup Points', 'wc-ukr-shipping'); ?>
            </a>
        </div>
    </div>
</div>

<div id="wcus-carriers"
     class="wcus-carriers"
     data-connect-message="<?php echo esc_attr(SmartyParcelHelper::getAccountRequiredMessage()); ?>"
     data-connect-button="<?php esc_attr_e('Connect SmartyParcel account', 'wc-ukr-shipping'); ?>">
    <?php foreach ($carriers as $carrier) { ?>
        <div class="wcus-carrier <?php echo $carrier['enabled'] ? '' : 'wcus-carrier--disabled'; ?>"
             data-carrier="<?php echo esc_attr($carrier['slug']); ?>">

            <div class="wcus-carrier__head">
                <img class="wcus-carrier__icon"
                     src="<?php echo esc_url($carrier['icon']); ?>"
                     alt="<?php echo esc_attr($carrier['name']); ?>">

                <div class="wcus-carrier__name"><?php echo esc_html($carrier['name']); ?></div>

                <label class="wcus-switcher wcus-carrier__switcher">
                    <input type="checkbox"
                           class="j-wcus-carrier-toggle"
                           value="1"
                           aria-label="<?php echo esc_attr($carrier['name']); ?>" <?php checked($carrier['enabled']); ?>>
                    <span class="wcus-switcher__control"></span>
                </label>
            </div>

            <div class="wcus-carrier__features">
                <?php foreach ($carrier['features'] as $feature) { ?>
                    <span class="wcus-carrier__feature"><?php echo esc_html($feature); ?></span>
                <?php } ?>
            </div>

            <div class="wcus-carrier__actions">
                <?php if ($isAccountConnected) { ?>
                    <a href="<?php echo esc_url($accountsUrl); ?>" class="wcus-btn wcus-btn--outline wcus-btn--xs">
                        <?php esc_html_e('Carrier accounts', 'wc-ukr-shipping'); ?>
                    </a>
                <?php } else { ?>
                    <button type="button"
                            class="wcus-btn wcus-btn--outline wcus-btn--xs j-wcus-carrier-accounts"
                            data-connect-url="<?php echo esc_url($connectUrl); ?>">
                        <?php esc_html_e('Carrier accounts', 'wc-ukr-shipping'); ?>
                    </button>
                <?php } ?>

                <?php if ($carrier['hasOptions']) { ?>
                    <button type="button" class="wcus-btn wcus-btn--outline wcus-btn--xs j-wcus-carrier-settings">
                        <?php esc_html_e('Settings', 'wc-ukr-shipping'); ?>
                    </button>
                <?php } ?>
            </div>

        </div>
    <?php } ?>
</div>
