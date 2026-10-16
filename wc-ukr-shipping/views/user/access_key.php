<?php
    if ( ! defined('ABSPATH')) {
        exit;
    }

    /** @var string $fieldName */
    /** @var string $accessKey */
    /** @var int $maxLength */
?>

<h2><?php esc_html_e('SmartyParcel', 'wc-ukr-shipping'); ?></h2>
<table class="form-table" role="presentation">
    <tr>
        <th>
            <label for="<?php echo esc_attr($fieldName); ?>"><?php esc_html_e('Access key', 'wc-ukr-shipping'); ?></label>
        </th>
        <td>
            <input type="text"
                   id="<?php echo esc_attr($fieldName); ?>"
                   name="<?php echo esc_attr($fieldName); ?>"
                   class="regular-text"
                   value="<?php echo esc_attr($accessKey); ?>"
                   maxlength="<?php echo (int)$maxLength; ?>"
                   autocomplete="off">
            <p class="description">
                <?php esc_html_e('Required to work with SmartyParcel in the admin panel (labels, tracking, etc.). The key defines what this user is allowed to do, e.g. labels only or full access, and is stored for this WordPress user only. The store owner receives it automatically when connecting the store; other users get it from the owner.', 'wc-ukr-shipping'); ?>
                <a href="<?php echo esc_url(WCUS_DOCS_ACCESS_KEYS_URL); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Learn more', 'wc-ukr-shipping'); ?></a>
            </p>
        </td>
    </tr>
</table>
