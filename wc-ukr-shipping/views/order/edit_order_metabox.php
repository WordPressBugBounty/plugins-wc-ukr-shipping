<div class="wcus-icon-block">
    <?php if ($shipping_label !== null) { ?>
        <div style="text-align: center;">
            <div class="wcus-label-widget wcus-label-widget--lg wcus-mb-1 <?php echo $carrier !== null ? 'wcus-label-widget__label--' . $carrier : ''; ?>">
                <?php echo esc_html($shipping_label['tracking_number']); ?>
                <?php if ($shipping_label['carrier_slug'] === 'wcus_pro') { ?>
                    <span style="color: #ff4500; font-size: 12px; margin-left: 4px;" title="WC Ukraine Shipping PRO">*</span>
                <?php } ?>
            </div>
            <?php if (!empty($shipping_label['carrier_status_code']) || !empty($shipping_label['carrier_status'])) { ?>
                <div class="wcus-text-center wcus-mb-1" style="color: #666;">
                    <?php echo esc_html($shipping_label['carrier_status']); ?>
                    [<?php echo esc_html($shipping_label['carrier_status_code']); ?>]
                </div>
            <?php } elseif (!empty($shipping_label['tracking_status'])) { ?>
                <div class="wcus-text-center wcus-mb-1" style="color: #666;">
                    Tracking API: <?php echo esc_html($shipping_label['tracking_status']); ?>
                </div>
            <?php } ?>
                <div class="wcus-mb-1">
                    <a href="#" class="wcus-svg-btn wcus-svg-btn--error j-wcus-label-delete"
                       style="font-size: 13px; color: #f00;"
                       data-label-id="<?php echo esc_attr($shipping_label['id']); ?>">
                        <?= __('Delete', 'wc-ukr-shipping-i18n'); ?>
                    </a>
                </div>
                <?php if ($shipping_label['label_id']) { ?>
                    <div>
                        <?php foreach ($download_formats as $format => $name) { ?>
                            <a href="<?php echo esc_attr(admin_url('admin.php?page=wc_ukr_shipping_print_label&label_id=' . $shipping_label['id'] . '&format=' . $format)); ?>"
                               target="_blank"
                               class="wcus-btn wcus-btn--xs wcus-btn--docs wcus-mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" style="width: 20px; height: 20px;">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V11C20.6569 11 22 12.3431 22 14V18C22 19.6569 20.6569 21 19 21H5C3.34314 21 2 19.6569 2 18V14C2 12.3431 3.34315 11 5 11V5ZM5 13C4.44772 13 4 13.4477 4 14V18C4 18.5523 4.44772 19 5 19H19C19.5523 19 20 18.5523 20 18V14C20 13.4477 19.5523 13 19 13V15C19 15.5523 18.5523 16 18 16H6C5.44772 16 5 15.5523 5 15V13ZM7 6V12V14H17V12V6H7ZM9 9C9 8.44772 9.44772 8 10 8H14C14.5523 8 15 8.44772 15 9C15 9.55228 14.5523 10 14 10H10C9.44772 10 9 9.55228 9 9ZM9 12C9 11.4477 9.44772 11 10 11H14C14.5523 11 15 11.4477 15 12C15 12.5523 14.5523 13 14 13H10C9.44772 13 9 12.5523 9 12Z" fill="#fff"/>
                                </svg>
                                <?php echo esc_html($name); ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
        </div>
    <?php } else { ?>
        <div style="text-align: center; padding: 16px;">
            <div class="wcus-mb-1">
                <a href="<?= admin_url('admin.php?page=wc_ukr_shipping_ttn&order_id=' . $order_id); ?>"
                   class="wcus-btn wcus-btn--docs wcus-btn--sm">
                    <?= __('Create shipping label', 'wc-ukr-shipping-i18n'); ?>
                </a>
            </div>
            <a class="wcus-btn wcus-btn--docs wcus-btn--sm j-wcus-label-attach" data-order-id="<?php echo esc_attr($order_id); ?>">
                <?= __('Attach shipping label', 'wc-ukr-shipping-i18n'); ?>
            </a>
        </div>
    <?php } ?>
</div>