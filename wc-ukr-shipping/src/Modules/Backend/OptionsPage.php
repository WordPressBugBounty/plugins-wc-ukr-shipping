<?php

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUkrShipping\Foundation\State;
use kirillbdev\WCUkrShipping\Http\Controllers\AddressBookController;
use kirillbdev\WCUkrShipping\Http\Controllers\MigrationController;
use kirillbdev\WCUkrShipping\Http\Controllers\OptionsController;
use kirillbdev\WCUkrShipping\Http\Controllers\SmartyParcelController;
use kirillbdev\WCUkrShipping\Model\Document\TTNStore;
use kirillbdev\WCUkrShipping\States\OptionsPageState;
use kirillbdev\WCUkrShipping\States\SmartyParcelState;
use kirillbdev\WCUkrShipping\States\WarehouseLoaderState;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\Foundation\View;
use kirillbdev\WCUSCore\Http\Routing\Route;

if ( ! defined('ABSPATH')) {
    exit;
}

class OptionsPage implements ModuleInterface
{
    private ShippingLabelsRepository $shippingLabelsRepository;

    public function __construct(ShippingLabelsRepository $shippingLabelsRepository)
    {
        $this->shippingLabelsRepository = $shippingLabelsRepository;
    }

    public function init()
    {
        add_action('admin_menu', [$this, 'registerOptionsPage'], 99);
        add_filter('wcus_load_admin_i18n', [$this, 'registerTranslates']);
    }

    public function routes()
    {
        return [
            new Route('wcus_save_options', OptionsController::class, 'save'),
            new Route('wcus_load_areas', AddressBookController::class, 'loadAreas'),
            new Route('wcus_load_cities', AddressBookController::class, 'loadCities'),
            new Route('wcus_load_warehouses', AddressBookController::class, 'loadWarehouses'),
            new Route('wcus_re_run_migrations', MigrationController::class, 'reRunMigrations'),
            new Route('wcus_smarty_parcel_register', SmartyParcelController::class, 'register'),
            new Route('wcus_smarty_parcel_check_verification', SmartyParcelController::class, 'checkVerification'),
            new Route('wcus_smarty_parcel_connect', SmartyParcelController::class, 'connect'),
            new Route('wcus_smarty_parcel_disconnect', SmartyParcelController::class, 'disconnect'),
            new Route('wcus_smarty_parcel_carrier_accounts', SmartyParcelController::class, 'getCarrierAccounts'),
            new Route('wcus_smarty_parcel_connect_carrier', SmartyParcelController::class, 'connectCarrier'),
            new Route('wcus_smarty_parcel_delete_carrier', SmartyParcelController::class, 'deleteCarrierAccount'),
            new Route('wcus_smarty_parcel_create_label', SmartyParcelController::class, 'createShippingLabel'),
            new Route('wcus_smarty_parcel_void_label', SmartyParcelController::class, 'voidLabel'),
        ];
    }

    public function registerOptionsPage()
    {
        State::add('warehouse_loader', WarehouseLoaderState::class);
        State::add('options', OptionsPageState::class);
        State::add('smarty_parcel', SmartyParcelState::class);

        add_menu_page(
            __('Settings', 'wc-ukr-shipping-i18n'),
            'WC Ukr Shipping',
            'manage_options',
            'wc_ukr_shipping_options',
            [$this, 'html'],
            WC_UKR_SHIPPING_PLUGIN_URL . 'image/menu-icon.png',
            56.15
        );

        add_submenu_page(
            'wc_ukr_shipping_options',
            __('Smarty Parcel', 'wc-ukr-shipping-i18n'),
            __('Smarty Parcel', 'wc-ukr-shipping-i18n'),
            'manage_options',
            'wcus_smarty_parcel',
            [$this, 'smartyParcelHtml']
        );

        add_submenu_page(
            null,
            __('Create TTN', 'wc-ukr-shipping-i18n'),
            __('Create TTN', 'wc-ukr-shipping-i18n'),
            'manage_options',
            'wc_ukr_shipping_ttn',
            [$this, 'ttnHtml']
        );
    }

    public function registerTranslates($i18n): array
    {
        return array_merge($i18n, [
            'warehouse_loader' => [
                'title' => __('Warehouses data of Nova Poshta', 'wc-ukr-shipping-i18n'),
                'last_update' => __('Last update date:', 'wc-ukr-shipping-i18n'),
                'status' => __('Status:', 'wc-ukr-shipping-i18n'),
                'status_not_completed' => __('Not completed', 'wc-ukr-shipping-i18n'),
                'status_completed' => __('Completed', 'wc-ukr-shipping-i18n'),
                'status_unknown' => __('Unknown', 'wc-ukr-shipping-i18n'),
                'update' => __('Update warehouses', 'wc-ukr-shipping-i18n'),
                'continue' => __('Continue update', 'wc-ukr-shipping-i18n'),
                'load_areas' => __('Load areas...', 'wc-ukr-shipping-i18n'),
                'load_cities' => __('Load cities...', 'wc-ukr-shipping-i18n'),
                'load_warehouses' => __('Load warehouses...', 'wc-ukr-shipping-i18n'),
                'success_updated' => __('Warehouses db updated successfully', 'wc-ukr-shipping-i18n'),
            ],
            'smarty_parcel' => [],
            'text_confirm_re_run_migrations' => __('Are you sure to restart migrations? This action cannot be canceled.', 'wc-ukr-shipping-i18n'),
        ]);
    }

    public function html()
    {
        echo View::render('settings');
    }

    public function smartyParcelHtml()
    {
        echo View::render('smarty_parcel');
    }

    public function ttnHtml(): void
    {
        if (get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) !== 'connected') {
            echo View::render('ttn_forbidden');
            return;
        }

        $label = $this->shippingLabelsRepository->findByOrderId((int)$_GET['order_id']);
        if ($label !== null) {
            return;
        }

        wp_enqueue_script(
            'wcus_ttn_form_js',
            WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/ttn-form.min.js',
            [ 'jquery' ],
            filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/ttn-form.min.js'),
            true
        );

        $store = new TTNStore((int)$_GET['order_id']);
        wp_localize_script('wcus_ttn_form_js', 'wcus_ttn_form_state', $store->collect());
        echo View::render('ttn');
    }
}
