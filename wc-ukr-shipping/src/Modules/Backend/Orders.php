<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use kirillbdev\WCUkrShipping\Http\WpHttpClient;
use kirillbdev\WCUSCore\Foundation\View;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class Orders implements ModuleInterface
{
    private ShippingLabelsRepository $shippingLabelsRepository;

    public function __construct(ShippingLabelsRepository $shippingLabelsRepository)
    {
        $this->shippingLabelsRepository = $shippingLabelsRepository;
    }

    public function init()
    {
        // Admin orders
        add_filter('manage_edit-shop_order_columns', [$this, 'extendOrderColumns']);
        add_action('manage_shop_order_posts_custom_column', [$this, 'renderTTNButton'], 10, 2);

        // Admin orders HPOS
        add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'extendOrderColumns']);
        add_action('manage_woocommerce_page_wc-orders_custom_column', [$this, 'renderTTNButtonHPOS'], 10, 2);

        add_action('init', [$this, 'handlePrintPage']);

        add_action('add_meta_boxes', [$this, 'addTTNBlockToOrderEdit']);
    }

    public function extendOrderColumns($columns)
    {
        $columns['wcus_ttn_actions'] = '<span class="wcus-sp-label-col">' . __('Shipping label', 'wc-ukr-shipping-i18n') . '</span>';

        return $columns;
    }

    public function renderTTNButton($column, $postId)
    {
        $this->renderTtnInfo(wc_get_order($postId), $column);
    }

    public function renderTTNButtonHPOS($column, $order)
    {
        $this->renderTtnInfo($order, $column);
    }

    public function handlePrintPage(): void
    {
        $isPrintLabelPage = isset($_GET['page'])
            && $_GET['page'] === 'wc_ukr_shipping_print_label'
            && isset($_GET['label_id'])
            && isset($_GET['format']);

        if (!is_admin() || !$isPrintLabelPage) {
            return;
        }

        if (!get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY)) {
            return;
        }

        $shippingLabel = $this->shippingLabelsRepository->findById((int)sanitize_text_field($_GET['label_id']));
        if ($shippingLabel === null) {
            return;
        }

        $format = sanitize_text_field($_GET['format']);
        if (!in_array($format, ['a4', 'm85', 'm100'], true)) {
            return;
        }

        header('Content-Type: application/pdf');
        $client = new WpHttpClient();
        echo $client->get(
            'https://api.smartyparcel.com/beta/labels/' . $shippingLabel['label_id'] . "/pdf?format=$format",
            null,
            [
                'SP-API-Key' =>  get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY),
            ]
        );
        exit;
    }

    public function addTTNBlockToOrderEdit()
    {
        /** @var CustomOrdersTableController $controller */
        $controller = wcus_wc_container_safe_get(CustomOrdersTableController::class);
        $screen = $controller !== null && $controller ->custom_orders_table_usage_is_enabled()
            ? wc_get_page_screen_id('shop-order')
            : 'shop_order';

        add_meta_box(
            'wcus_edit_order_ttn_metabox',
            __('WC Ukraine Shipping', 'wc-ukr-shipping-i18n'),
            [$this, 'editOrderTTNMetaboxHtml'],
            $screen,
            'side'
        );
    }

    public function editOrderTTNMetaboxHtml($editedOrder)
    {
        $order = ($editedOrder instanceof \WP_Post) ? wc_get_order($editedOrder->ID) : $editedOrder;

        if (!$order || !$order->has_shipping_method(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            echo __('Invoice creation is unavailable for this order', 'wc-ukr-shipping-i18n');

            return;
        }

        $data['shipping_label'] = $this->shippingLabelsRepository->findByOrderId((int)$order->get_id());
        $data['order_id'] = $order->get_id();

        echo View::render('order/edit_order_metabox', $data);
    }

    private function renderTtnInfo($order, string $column): void
    {
        if (!$order->has_shipping_method(WC_UKR_SHIPPING_NP_SHIPPING_NAME)) {
            return;
        }

        if ($column === 'wcus_ttn_actions') {
            $ttn = $this->shippingLabelsRepository->findByOrderId((int)$order->get_id());
            ?>
                <?php if ($ttn !== null) { ?>
                    <div class="wcus-icon-block" style="text-align: center;">
                        <div class="wcus-label-widget j-wcus-label-widget">
                            <?php echo esc_html($ttn['tracking_number']); ?>
                        </div>
                        <div style="text-align: center;">
                            <a href="#" class="wcus-svg-btn wcus-svg-btn--error j-wcus-label-delete"
                               style="font-size: 13px; color: #f00;"
                               data-label-id="<?php echo esc_attr($ttn['id']); ?>">
                                <?= __('Delete', 'wc-ukr-shipping-i18n'); ?>
                            </a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div style="text-align: center;">
                        <a href="<?= admin_url('admin.php?page=wc_ukr_shipping_ttn&order_id=' . $order->get_id()); ?>"
                           class="wcus-svg-btn">
                            <?= __('Create', 'wc-ukr-shipping-i18n'); ?>
                        </a>
                    </div>
            <?php } ?>
            <?php
        }
    }
}
