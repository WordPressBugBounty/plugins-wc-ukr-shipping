<?php

if (!defined('ABSPATH')) {
    exit;
}

// Shipping methods
define('WCUS_SHIPPING_METHOD_NOVA_POSHTA', 'nova_poshta_shipping');
define('WCUS_SHIPPING_METHOD_UKRPOSHTA', 'wcus_ukrposhta_shipping');
define('WCUS_SHIPPING_METHOD_UKRPOSHTA_ADDRESS', 'wcus_ukrposhta_address_shipping');
define('WCUS_SHIPPING_METHOD_NOVA_POST', 'wcus_nova_post_shipping');
define('WCUS_SHIPPING_METHOD_NOVA_POST_ADDRESS', 'wcus_nova_post_address');
define('WCUS_SHIPPING_METHOD_ROZETKA', 'wcus_rozetka_delivery_shipping');
define('WCUS_SHIPPING_METHOD_MEEST', 'wcus_meest_shipping');
define('WCUS_SHIPPING_METHOD_MEEST_ADDRESS', 'wcus_meest_address_shipping');
define('WCUS_SHIPPING_METHOD_NOVA_GLOBAL_ADDRESS', 'wcus_nova_global_address');
define('WCUS_SHIPPING_METHOD_POST_NORD', 'wcus_post_nord_shipping');
define('WCUS_SHIPPING_METHOD_POST_NORD_ADDRESS', 'wcus_post_nord_address');
define('WCUS_SHIPPING_METHOD_INPOST', 'wcus_inpost_shipping');
define('WCUS_SHIPPING_METHOD_INPOST_ADDRESS', 'wcus_inpost_address_shipping');
define('WCUS_SHIPPING_METHOD_GLS', 'wcus_gls_shipping');
define('WCUS_SHIPPING_METHOD_GLS_ADDRESS', 'wcus_gls_address_shipping');
define('WCUS_SHIPPING_METHOD_DHL_EXPRESS_ADDRESS', 'wcus_dhl_express_address');

// Shipping item meta
define('WCUS_SHIPPING_META_VIEW_COST', 'wcus_view_cost');
define('WCUS_SHIPPING_META_SERVICE_TYPE', 'wcus_service_type');

// Warehouse Type
define('WCUS_WAREHOUSE_TYPE_REGULAR', 1);
define('WCUS_WAREHOUSE_TYPE_CARGO', 2);
define('WCUS_WAREHOUSE_TYPE_POSHTOMAT', 3);

// Shipment defaults, used when the order data is not enough to calculate rates
define('WCUS_DEFAULT_PARCEL_WEIGHT', 1);
define('WCUS_DEFAULT_PARCEL_WIDTH', 10);
define('WCUS_DEFAULT_PARCEL_HEIGHT', 10);
define('WCUS_DEFAULT_PARCEL_LENGTH', 10);
define('WCUS_DEFAULT_COD_PAYMENT_METHOD', 'cod');

// Options
define('WCUS_OPTION_SAVE_CUSTOMER_ADDRESS', 'wc_ukr_shipping_np_save_warehouse');
define('WCUS_OPTION_SHOW_POSHTOMATS', 'wcus_show_poshtomats');
define('WCUS_OPTION_SMARTY_PARCEL_API_KEY', 'wcus_smartyparcel_api_key');
define('WCUS_OPTION_SMARTY_PARCEL_USER_STATUS', 'wcus_smartyparcel_user_status');
define('WCUS_OPTION_SMARTY_PARCEL_CARRIERS', 'wcus_smartyparcel_carriers');

define('WCUS_OPTION_SMARTY_PARCEL_GUEST_ID', 'wcus_smartyparcel_guest_id');
// Version of the consent text the owner accepts (guest connect, help assistant)
define('WCUS_SMARTY_PARCEL_CONSENT_VERSION', '2026-11-09');

// SmartyParcel SDK URLs, overridable via the wcus_smartyparcel_sdk_urls filter (SmartyParcelHelper::getSdkUrl())
define('WCUS_SMARTY_PARCEL_ELEMENTS_SDK_URL', 'https://app.smartyparcel.com/static/embedded/elements-sdk.min.js');
define('WCUS_SMARTY_PARCEL_ASSISTANT_SDK_URL', 'https://app.smartyparcel.com/static/embedded/assistant.min.js');

define('WCUS_SMARTY_PARCEL_ASSISTANT_API_URL', 'https://wp-api.smartyparcel.com/assistant/v1');

// SmartyParcel help assistant (works without a platform connection, has its own key)
define('WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_KEY', 'wcus_smartyparcel_assistant_key');
define('WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_TOKEN', 'wcus_smartyparcel_assistant_token');

// User meta
define('WCUS_USER_META_SMARTY_PARCEL_ACCESS_KEY', 'wcus_smartyparcel_access_key');
define('WCUS_USER_META_SMARTY_PARCEL_JWT', 'wcus_smartyparcel_jwt');

// Docs
define('WCUS_DOCS_ACCESS_KEYS_URL', 'https://smartyparcel.com/docs/elements-access-keys-in-woocommerce/');