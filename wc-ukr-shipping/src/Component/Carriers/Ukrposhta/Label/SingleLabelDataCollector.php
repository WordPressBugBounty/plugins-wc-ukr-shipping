<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Label;

use kirillbdev\WCUkrShipping\Factories\ProductFactory;
use kirillbdev\WCUkrShipping\Foundation\UkrPoshtaShipping;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Services\Calculation\ProductDimensionService;

class SingleLabelDataCollector
{
    private array $data;
    private \WC_Order $order;
    private \WC_Order_Item_Shipping $orderShipping;
    private array $orderProducts;
    private ProductDimensionService $productDimensionService;

    public function __construct(\WC_Order $order)
    {
        $this->order = $order;
        $this->orderShipping = WCUSHelper::getOrderShippingMethod($this->order);
        $this->data = [];

        $factory = new ProductFactory();
        $this->productDimensionService = wcus_container()->make(ProductDimensionService::class);

        foreach ($this->order->get_items() as $item) {
            /** @var \WC_Order_Item_Product $item */
            $product = $factory->makeOrderItemProduct($item);
            $this->orderProducts[] = $product;
        }
    }

    public function collect(): array
    {
        $this->data['carrier'] = 'ukrposhta';

        $this->collectCommonData();
        $this->collectSender();
        $this->collectParcelsData();
        $this->collectRecipient();

        return $this->data;
    }

    private function collectCommonData(): void
    {
        $this->data['order_id'] = $this->order->get_id();

        $shippingMethod = new UkrPoshtaShipping((int)$this->orderShipping->get_instance_id());
        $this->data['common']['service_type'] = 'ukrposhta_' . strtolower($shippingMethod->get_option('service_type'));

        $this->data['common']['paid_by'] = 'recipient'; // hardcoded yet
        $this->data['common']['description'] = apply_filters('wcus_ttn_form_description', 'Order #' . $this->order->get_id(), $this->order);
        $this->data['common']['external_order_id'] = $this->order->get_id();
        $this->data['common']['declared_price'] = $this->getDeclaredPrice();
    }

    private function collectParcelsData(): void
    {
        $dimensions = $this->productDimensionService->getTotalDimensions($this->orderProducts);

        $this->data['common']['parcels'] = [
            [
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'length' => $dimensions['length'],
                'weight' => $this->calculateWeight(),
            ]
        ];
    }

    private function collectSender(): void
    {
        $accounts = array_values(
            array_filter($this->getCarrierAccountCached(), function (array $account) {
                return ($account['carrier_slug'] ?? '') === 'ukrposhta';
            })
        );

        $defaultAcc = wc_ukr_shipping_get_option('wcus_ukrposhta_default_carrier');
        if (empty($defaultAcc)) {
            $defaultAcc = $accounts[0]['id'] ?? '';
        }

        $this->data['carrier_accounts'] = $accounts;
        $this->data['sender']['carrier_account_id'] = $defaultAcc;

        $sender = json_decode(wc_ukr_shipping_get_option('wcus_ukrposhta_ttn_sender'), true);

        $this->data['sender']['first_name'] = $sender['first_name'] ?? '';
        $this->data['sender']['last_name'] = $sender['last_name'] ?? '';
        $this->data['sender']['middle_name'] = $sender['middle_name'] ?? '';
        $this->data['sender']['phone'] = $sender['phone'] ?? '';
        $this->data['sender']['city'] = [
            'value' => '',
            'name' => '',
        ];
        $this->data['sender']['warehouse'] = [
            'value' => '',
            'name' => '',
        ];
    }

    private function collectRecipient(): void
    {
        $maybeDifferentAddress = (int)$this->order->get_meta('wc_ukr_shipping_np_different_address');
        $shippingMethod = WCUSHelper::getOrderShippingMethod($this->order);

        $this->data['recipient'] = [
            'first_name' => $maybeDifferentAddress
                ? $this->order->get_shipping_first_name()
                : $this->order->get_billing_first_name(),
            'last_name' => $maybeDifferentAddress
                ? $this->order->get_shipping_last_name()
                : $this->order->get_billing_last_name(),
            'middle_name' => $this->order->get_meta('wcus_middlename'),
            'phone' => $maybeDifferentAddress && $this->order->get_meta('wcus_shipping_phone')
                ? $this->order->get_meta('wcus_shipping_phone')
                : $this->order->get_billing_phone(),
            'city' => [
                'value' => $shippingMethod->get_meta('wcus_ukrposhta_city_id'),
                'name' => $shippingMethod->get_meta('wcus_ukrposhta_city_name') ?: '-',
            ],
            'warehouse' => [
                'value' => $shippingMethod->get_meta('wcus_ukrposhta_warehouse_id'),
                'name' => $shippingMethod->get_meta('wcus_ukrposhta_warehouse_name') ?: '-',
            ],
        ];
    }

    private function getCarrierAccountCached(): array
    {
        $carrierAccounts = get_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS);
        if ($carrierAccounts) {
            $carrierAccounts = json_decode($carrierAccounts, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $carrierAccounts;
            }
        }

        return [];
    }

    private function calculateWeight(): float
    {
        $defaultWeight = wc_ukr_shipping_get_option('wcus_ttn_weight_default') ?: 0.1;
        $weight = 0;

        foreach ($this->orderProducts as $product) {
            $weight += $product->getWeight() * $product->getQuantity();
        }

        return max($weight, (float)$defaultWeight);
    }

    private function getDeclaredPrice(): float
    {
        return $this->order->get_subtotal() + (float)$this->order->get_total_fees() + (float)$this->order->get_total_tax('') - $this->order->get_total_discount();
    }
}
