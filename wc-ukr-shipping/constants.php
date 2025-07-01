<?php

if (!defined('ABSPATH')) {
    exit;
}

// Shipping methods
define('WCUS_SHIPPING_METHOD_UKRPOSHTA', 'wcus_ukrposhta_shipping');
define('WCUS_SHIPPING_METHOD_NOVA_POST', 'wcus_nova_post_shipping');
define('WCUS_SHIPPING_METHOD_ROZETKA', 'wcus_rozetka_delivery_shipping');

// Warehouse Type
define('WCUS_WAREHOUSE_TYPE_REGULAR', 1);
define('WCUS_WAREHOUSE_TYPE_CARGO', 2);
define('WCUS_WAREHOUSE_TYPE_POSHTOMAT', 3);

// Options
define('WCUS_OPTION_SAVE_CUSTOMER_ADDRESS', 'wc_ukr_shipping_np_save_warehouse');
define('WCUS_OPTION_SHOW_POSHTOMATS', 'wcus_show_poshtomats');
define('WCUS_OPTION_LOADER_LAST_SYNC', 'wcus_loader_last_sync');
define('WCUS_OPTION_SMARTY_PARCEL_API_KEY', 'wcus_smartyparcel_api_key');
define('WCUS_OPTION_SMARTY_PARCEL_USER_STATUS', 'wcus_smartyparcel_user_status');
define('WCUS_OPTION_SMARTY_PARCEL_APP_STATUS', 'wcus_smartyparcel_app_status');
define('WCUS_OPTION_SMARTY_PARCEL_CARRIERS', 'wcus_smartyparcel_carriers');