<?php

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Foundation\State;
use kirillbdev\WCUkrShipping\Http\Controllers\AddressBookController;
use kirillbdev\WCUkrShipping\Http\Controllers\MigrationController;
use kirillbdev\WCUkrShipping\Http\Controllers\OptionsController;
use kirillbdev\WCUkrShipping\States\WarehouseLoaderState;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\Foundation\View;
use kirillbdev\WCUSCore\Http\Routing\Route;

if ( ! defined('ABSPATH')) {
    exit;
}

class OptionsPage implements ModuleInterface
{
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
        ];
    }

    public function registerOptionsPage()
    {
        State::add('warehouse_loader', WarehouseLoaderState::class);

        add_menu_page(
            __('Settings', 'wc-ukr-shipping-i18n'),
            'WC Ukr Shipping',
            'manage_options',
            'wc_ukr_shipping_options',
            [$this, 'html'],
            WC_UKR_SHIPPING_PLUGIN_URL . 'image/menu-icon.png',
            56.15
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
            'text_confirm_re_run_migrations' => __('Are you sure to restart migrations? This action cannot be canceled.', 'wc-ukr-shipping-i18n'),
        ]);
    }

    public function html()
    {
        echo View::render('settings');
    }

    public function premiumHtml()
    {
        wp_redirect('https://kirillbdev.pro/wc-ukr-shipping-pro/?ref=plugin_menu', 301);
        exit;
    }
}