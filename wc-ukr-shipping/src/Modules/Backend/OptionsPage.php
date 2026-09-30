<?php

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Component\ListTable\AutomationListTable;
use kirillbdev\WCUkrShipping\DB\Repositories\AutomationRulesRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUkrShipping\Foundation\State;
use kirillbdev\WCUkrShipping\Helpers\SmartyParcelHelper;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Http\Controllers\AddressBookController;
use kirillbdev\WCUkrShipping\Http\Controllers\AutomationController;
use kirillbdev\WCUkrShipping\Http\Controllers\CarriersController;
use kirillbdev\WCUkrShipping\Http\Controllers\OptionsController;
use kirillbdev\WCUkrShipping\Http\Controllers\SmartyParcelController;
use kirillbdev\WCUkrShipping\Http\Middleware\CheckManageWooPermission;
use kirillbdev\WCUkrShipping\Http\Resources\OrderResource;
use kirillbdev\WCUkrShipping\Model\Document\TTNStore;
use kirillbdev\WCUkrShipping\Services\CarrierService;
use kirillbdev\WCUkrShipping\States\OrdersState;
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
    private AutomationRulesRepository $automationRulesRepository;
    private CarrierService $carrierService;
    private AutomationListTable $table;

    public function __construct(
        ShippingLabelsRepository $shippingLabelsRepository,
        AutomationRulesRepository $automationRulesRepository,
        CarrierService $carrierService
    ) {
        $this->shippingLabelsRepository = $shippingLabelsRepository;
        $this->automationRulesRepository = $automationRulesRepository;
        $this->carrierService = $carrierService;
    }

    public function init()
    {
        add_action('admin_menu', [$this, 'registerOptionsPage'], 99);
        add_filter('wcus_load_admin_i18n', [$this, 'registerTranslates']);
    }

    public function routes()
    {
        $map = [
            'wcus_save_options' => [OptionsController::class, 'save'],

            // Carriers
            'wcus_toggle_carrier' => [CarriersController::class, 'toggle'],
            'wcus_get_carrier_options' => [CarriersController::class, 'getOptions'],
            'wcus_save_carrier_options' => [CarriersController::class, 'saveOptions'],

            'wcus_load_areas' => [AddressBookController::class, 'loadAreas'],
            'wcus_load_cities' => [AddressBookController::class, 'loadCities'],
            'wcus_load_warehouses' => [AddressBookController::class, 'loadWarehouses'],

            'wcus_smartyparcel_api' => [SmartyParcelController::class, 'sendApiRequest'],
            'wcus_smartyparcel_disconnect' => [SmartyParcelController::class, 'disconnect'],
            'wcus_smarty_parcel_create_label' => [SmartyParcelController::class, 'createShippingLabel'],
            'wcus_smarty_parcel_create_label_batch' => [SmartyParcelController::class, 'createLabelBatch'],
            'wcus_smarty_parcel_void_label' => [SmartyParcelController::class, 'voidLabel'],
            'wcus_tracking_form_init' => [SmartyParcelController::class, 'initTrackingForm'],
            'wcus_attach_label' => [SmartyParcelController::class, 'attachShippingLabel'],
            'wcus_automation_save_rule' => [AutomationController::class, 'saveRule'],
        ];

        $routes = [];
        foreach ($map as $action => [$controller, $method]) {
            $options = $action !== 'wcus_smartyparcel_api'
                ? ['middleware' => [CheckManageWooPermission::class]]
                : [];

            $routes[] = new Route($action, $controller, $method, $options);
        }

        return $routes;
    }

    public function registerOptionsPage()
    {
        State::add('warehouse_loader', WarehouseLoaderState::class);
        State::add('orders', OrdersState::class);

        add_menu_page(
            __('Dashboard', 'wc-ukr-shipping'),
            'SmartyParcel',
            'manage_options',
            'wcus_smarty_parcel',
            [$this, 'smartyParcelHtml'],
            WC_UKR_SHIPPING_PLUGIN_URL . 'image/menu-icon.png',
            56.15
        );

        add_submenu_page(
            'wcus_smarty_parcel',
            __('Dashboard', 'wc-ukr-shipping'),
            __('Dashboard', 'wc-ukr-shipping'),
            'manage_options',
            'wcus_smarty_parcel',
            [$this, 'smartyParcelHtml']
        );

        add_submenu_page(
            'wcus_smarty_parcel',
            __('Settings', 'wc-ukr-shipping'),
            __('Settings', 'wc-ukr-shipping'),
            'manage_options',
            'wcus_settings',
            [$this, 'html']
        );

        add_submenu_page(
            '',
            __('Create TTN', 'wc-ukr-shipping'),
            __('Create TTN', 'wc-ukr-shipping'),
            'manage_woocommerce',
            'wc_ukr_shipping_ttn',
            [$this, 'ttnHtml']
        );

        add_submenu_page(
            'wcus_smarty_parcel',
            __('Orders', 'wc-ukr-shipping'),
            __('Orders', 'wc-ukr-shipping'),
            'manage_woocommerce',
            'wc_ukr_shipping_ttn_list',
            [$this, 'orderListHtml']
        );

        $automationPage = add_submenu_page(
            'wcus_smarty_parcel',
            __('Automation', 'wc-ukr-shipping'),
            __('Automation', 'wc-ukr-shipping'),
            'manage_woocommerce',
            'wcus_automation',
            [$this, 'automationHtml']
        );
        add_action("load-$automationPage", function () {
            $this->table = new AutomationListTable($this->automationRulesRepository);
        });

        add_submenu_page(
            '',
            __('Create', 'wc-ukr-shipping'),
            __('Create', 'wc-ukr-shipping'),
            'manage_woocommerce',
            'wcus_automation_rule_create',
            [$this, 'automationRuleFormHtml']
        );

        add_submenu_page(
            '',
            __('Edit', 'wc-ukr-shipping'),
            __('Edit', 'wc-ukr-shipping'),
            'manage_woocommerce',
            'wcus_automation_rule_edit',
            [$this, 'automationRuleFormHtml']
        );
    }

    public function registerTranslates($i18n): array
    {
        return array_merge($i18n, [
            'warehouse_loader' => [
                'title' => __('Warehouses data of Nova Poshta', 'wc-ukr-shipping'),
                'last_update' => __('Last update date:', 'wc-ukr-shipping'),
                'status' => __('Status:', 'wc-ukr-shipping'),
                'status_not_completed' => __('Not completed', 'wc-ukr-shipping'),
                'status_completed' => __('Completed', 'wc-ukr-shipping'),
                'status_unknown' => __('Unknown', 'wc-ukr-shipping'),
                'update' => __('Update warehouses', 'wc-ukr-shipping'),
                'continue' => __('Continue update', 'wc-ukr-shipping'),
                'load_areas' => __('Load areas...', 'wc-ukr-shipping'),
                'load_cities' => __('Load cities...', 'wc-ukr-shipping'),
                'load_warehouses' => __('Load warehouses...', 'wc-ukr-shipping'),
                'success_updated' => __('Warehouses db updated successfully', 'wc-ukr-shipping'),
            ],
            'smarty_parcel' => [],
            'text_confirm_re_run_migrations' => __('Are you sure to restart migrations? This action cannot be canceled.', 'wc-ukr-shipping'),
        ]);
    }

    public function html(): void
    {
        echo View::render('settings_general', [
            'carriers' => $this->carrierService->getCarrierList(),
        ]);
    }

    public function smartyParcelHtml()
    {
        echo View::render('smarty_parcel');
    }

    public function ttnHtml(): void
    {
        if (!SmartyParcelHelper::isConnected()) {
            echo View::render('ttn/ttn_forbidden');
            return;
        }

        $label = $this->shippingLabelsRepository->findByOrderId((int)$_GET['order_id']);
        if ($label !== null) {
            return;
        }

        $order = wc_get_order((int)$_GET['order_id']);
        if ( ! $order) {
            throw new \InvalidArgumentException('Order #' . (int)$_GET['order_id'] . ' not found.');
        }
        $shippingMethod = WCUSHelper::getOrderShippingMethod($order);

        // Hardcoded yet: detect and process elements-sdk flow
        $legacyFormMethods = [
            WCUS_SHIPPING_METHOD_NOVA_POSHTA,
        ];
        if ($shippingMethod === null || !in_array($shippingMethod->get_method_id(), $legacyFormMethods, true)) {
            $this->processPurchaseLabelV2($order);
            return;
        }

        wp_enqueue_script(
            'wcus_ttn_form_js',
            WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/ttn-form.min.js',
            [ 'jquery' ],
            filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/ttn-form.min.js'),
            true
        );

        if (isset($_GET['carrier'])) {
            $carrier = $_GET['carrier'];
        } else {
            $carrier = SmartyParcelHelper::getOrderCarrierSlug($order);
        }

        $store = null;
        switch ($carrier) {
            case 'nova_poshta':
                $store = new TTNStore((int)$_GET['order_id']);
                break;
        }

        if ($store === null) {
            echo 'Unable to init shipment form';
        } else {
            wp_localize_script('wcus_ttn_form_js', 'wcus_ttn_form_state', $store->collect());
            echo View::render('ttn/ttn');
        }
    }

    public function processPurchaseLabelV2(\WC_Order $order): void
    {
        wp_enqueue_script(
        'smartyparcel_labels_js',
            WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/labels.min.js',
            ['smartyparcel_elements_sdk_js'],
            filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/labels.min.js'),
            true
        );

        $orderData = (new OrderResource($order))->toElementsData();

        wp_localize_script('smartyparcel_labels_js', 'wcus_sp_order_data', $orderData);

        echo View::render('ttn/ttn_v2');
    }

    public function orderListHtml(): void
    {
        echo View::render('orders');
    }

    public function automationHtml()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            if (wp_verify_nonce($_GET['_wpnonce'] ?? '', 'wcus_automation_delete')) {
                $this->automationRulesRepository->delete((int)$_GET['id']);
            }
        }

        $this->table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
            <a href="<?php echo esc_attr(admin_url('admin.php?page=wcus_automation_rule_create')); ?>"
               class="page-title-action"><?php esc_html_e('Add rule', 'wc-ukr-shipping'); ?></a>
            <hr class="wp-header-end">
            <form action="" method="POST">
                <?php $this->table->display(); ?>
            </form>
        </div>
        <?php
    }

    public function automationRuleFormHtml(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $model = null;
        if ($id > 0) {
            $model = $this->automationRulesRepository->findById($id);
            if ($model === null) {
                echo sprintf(
                    '<div class="notice notice-error">%s</div>',
                    esc_html(__('Rule not found', 'wc-ukr-shipping'))
                );
                return;
            }
        }

        echo View::render('automation', [
            'model' => $model,
            'successMsg' => isset($_GET['success']) && $_GET['success'] === '1'
                ? __('Rule saved successfully', 'wc-ukr-shipping')
                : null,
        ]);
    }
}
