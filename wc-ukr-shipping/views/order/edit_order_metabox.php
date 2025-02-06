<div class="wcus-icon-block">
    <?php if ($shipping_label !== null) { ?>
        <div style="text-align: center;">
            <div style="display: inline-block; padding: 4px 12px; font-weight: 600; border-radius: 3px; background: #ddd;"><?php echo esc_html($shipping_label['tracking_number']); ?></div>
            <div style="text-align: center;">
                <a href="#" class="wcus-svg-btn wcus-svg-btn--error j-wcus-label-delete"
                   style="font-size: 13px; color: #f00;"
                   data-label-id="<?php echo esc_attr($shipping_label['id']); ?>">
                    <?= __('Delete', 'wc-ukr-shipping-i18n'); ?>
                </a>
            </div>
        </div>
    <?php } else { ?>
        <div style="text-align: center; padding: 16px;">
          <a href="<?= admin_url('admin.php?page=wc_ukr_shipping_ttn&order_id=' . $order_id); ?>"
             class="wcus-btn wcus-btn--docs wcus-btn--sm">
              <?= __('Create shipping label', 'wc-ukr-shipping-pro'); ?>
          </a>
        </div>
    <?php } ?>
</div>