<div id="wcus-pane-shipping" class="wcus-tab-pane">

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="checkbox" name="wcus[cost_view_only]" value="1" <?php echo (int)get_option('wcus_cost_view_only') === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('Calculate shipping cost for view only', 'wc-ukr-shipping-i18n'); ?></div>
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_price"><?= __('Shipping cost', 'wc-ukr-shipping-i18n'); ?></label>
        <input type="number" id="wc_ukr_shipping_np_price"
               name="wc_ukr_shipping[np_price]"
               class="wcus-form-control"
               min="0"
               step="0.000001"
               value="<?= get_option('wc_ukr_shipping_np_price', 0); ?>">
    </div>

    <div class="wcus-form-group">
        <label for="wc_ukr_shipping_np_block_pos"><?= __('Shipping block position on checkout page', 'wc-ukr-shipping-i18n'); ?></label>
        <select id="wc_ukr_shipping_np_block_pos"
                name="wc_ukr_shipping[np_block_pos]"
                class="wcus-form-control">
            <option value="billing" <?= wc_ukr_shipping_get_option('wc_ukr_shipping_np_block_pos') === 'billing' ? 'selected' : ''; ?>><?= __('Default section', 'wc-ukr-shipping-i18n'); ?></option>
            <option value="additional" <?= wc_ukr_shipping_get_option('wc_ukr_shipping_np_block_pos') === 'additional' ? 'selected' : ''; ?>><?= __('Additional section', 'wc-ukr-shipping-i18n'); ?></option>
        </select>
    </div>

    <div class="wcus-form-group wcus-form-group--horizontal">
        <label class="wcus-switcher">
            <input type="hidden" name="wc_ukr_shipping[address_shipping]" value="0">
            <input type="checkbox" name="wc_ukr_shipping[address_shipping]" value="1" <?= (int)get_option('wc_ukr_shipping_address_shipping', 1) === 1 ? 'checked' : ''; ?>>
            <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('Enable address shipping', 'wc-ukr-shipping-i18n'); ?></div>
    </div>

    <?php /* Store last warehouse */ ?>
    <div class="wcus-form-group">
      <div class="wcus-form-group--horizontal">
        <label class="wcus-switcher">
          <input type="hidden" name="wc_ukr_shipping[np_save_warehouse]" value="0">
          <input type="checkbox" name="wc_ukr_shipping[np_save_warehouse]" value="1" <?= (int)get_option(WCUS_OPTION_SAVE_CUSTOMER_ADDRESS) === 1 ? 'checked' : ''; ?>>
          <span class="wcus-switcher__control"></span>
        </label>
        <div class="wcus-control-label"><?= __('Save last customer address', 'wc-ukr-shipping-i18n'); ?></div>
      </div>
      <div class="wcus-form-group__tooltip"><?= __('This option is not working with old UI', 'wc-ukr-shipping-i18n'); ?></div>
    </div>

</div>