<?php

namespace kirillbdev\WCUkrShipping\Model\Document;

use kirillbdev\WCUkrShipping\Address\Provider\AddressProviderInterface;
use kirillbdev\WCUkrShipping\Factories\ProductFactory;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Includes\Address\RepositoryCityFinder;
use kirillbdev\WCUkrShipping\Includes\Address\RepositoryWarehouseFinder;
use kirillbdev\WCUkrShipping\Includes\UI\CityUIValue;
use kirillbdev\WCUkrShipping\Includes\UI\WarehouseUIValue;
use kirillbdev\WCUkrShipping\Model\OrderProduct;
use kirillbdev\WCUkrShipping\Services\TranslateService;

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Tiny version of WC Ukraine Shipping PRO
 */
class TTNStore
{
    /**
     * @var \WC_Order
     */
    private $order;

    /**
     * @var TranslateService
     */
    private $translateService;

    /**
     * @var \WC_Order_Item_Shipping
     */
    private $orderShipping;

    /**
     * @var OrderProduct[]
     */
    private $orderProducts = [];

    /**
     * @var NovaPoshtaApi
     */
    private $api;

    /**
     * @var array
     */
    private $data = [];

    public function __construct(int $orderId)
    {
        $this->order = wc_get_order($orderId);
        if ( ! $this->order) {
            throw new \InvalidArgumentException('Order #' . sanitize_text_field($orderId) . ' not found.');
        }

        $this->translateService = new TranslateService();
        $this->orderShipping = WCUSHelper::getOrderShippingMethod($this->order);

        $factory = new ProductFactory();

        foreach ($this->order->get_items() as $item) {
            /** @var \WC_Order_Item_Product $item */
            $product = $factory->makeOrderItemProduct($item);
            $this->orderProducts[] = $product;
        }
    }

    public function collect()
    {
        $this->collectCommonData();
        $this->calculateCost();
        $this->collectSender();
        $this->collectRecipient();
        $this->collectHelpers();

        return apply_filters('wcus_collect_ttn_form', $this->data, $this->order);
    }

    private function collectCommonData()
    {
        $date = apply_filters('wcus_ttn_form_date', new \DateTime(), $this->order);
        if (!($date instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException("Parameter 'date' must be correct date");
        }

        $this->data['ttn'] = [
            'order_id' => $this->order->get_id(),
            'global_params' => 1,
            'weight' => $this->calculateWeight(),
            'date' => $date->format('Y-m-d'),
            'description' => apply_filters('wcus_ttn_form_description', 'Order #' . $this->order->get_id(), $this->order),
            'barcode' => apply_filters('wcus_ttn_form_barcode', '', $this->order),
            'additional' => apply_filters('wcus_ttn_form_additional', '', $this->order)
        ];
    }

    private function calculateWeight(): float
    {
        $weight = 0;

        foreach ($this->orderProducts as $product) {
            $weight += $product->getWeight() * $product->getQuantity();
        }

        return max($weight, 0.1);
    }

    private function calculateCost(): void
    {
        $this->data['ttn']['cost'] = apply_filters('wcus_ttn_form_cost', $this->getShipmentCost(), $this->order);
    }

    private function collectSender(): void
    {
        $accounts = $this->getCarrierAccountCached();

        $this->data['sender']['carrier_accounts'] = $accounts;
        $this->data['sender']['carrier_account_id'] = $accounts[0]['id'] ?? '';
        $this->data['sender']['area_ref'] = '';

        $cityFinder = new RepositoryCityFinder(
            wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_city')
        );
        $warehouseFinder = new RepositoryWarehouseFinder(
            wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_warehouse')
        );

        $this->data['sender']['default_city'] = CityUIValue::fromFinder($cityFinder);
        $this->data['sender']['city_ref'] = $this->data['sender']['default_city']['value'];

        $this->data['sender']['default_warehouse'] = WarehouseUIValue::fromFinder($warehouseFinder);
        $this->data['sender']['warehouse_ref'] = $this->data['sender']['default_warehouse']['value'];
    }

    private function collectRecipient(): void
    {
        $maybeDifferentAddress = (int)$this->order->get_meta('wc_ukr_shipping_np_different_address');

        $data['firstname'] = $maybeDifferentAddress
            ? $this->order->get_shipping_first_name()
            : $this->order->get_billing_first_name();

        $data['lastname'] = $maybeDifferentAddress
            ? $this->order->get_shipping_last_name()
            : $this->order->get_billing_last_name();

        $data['middlename'] = $this->order->get_meta('wcus_middlename');

        $data['phone'] = $this->order->get_billing_phone();
        if ($maybeDifferentAddress && $this->order->get_meta('wcus_shipping_phone')) {
            $data['phone'] = $this->order->get_meta('wcus_shipping_phone');
        }
        $data['email'] = $this->order->get_billing_email();

        $this->data['recipient']['firstname'] = $data['firstname'];
        $this->data['recipient']['lastname'] = $data['lastname'];
        $this->data['recipient']['middlename'] = $data['middlename'];
        $this->data['recipient']['phone'] = $data['phone'];
        $this->data['recipient']['email'] = $data['email'];
        $this->data['recipient']['type'] = 'private_person';

        $shippingAddress = $this->order->has_shipping_method(WC_UKR_SHIPPING_NP_SHIPPING_NAME)
            ? new ShippingRecipientAddress($this->order, $this->orderShipping)
            : new CustomRecipientAddress($this->order);

        $shippingAddress->writeData($this->data);
    }

    private function collectHelpers()
    {
        $this->data['helpers']['default_cities'] = array_map(function($item) {
            return [
                'name' => $item[$this->translateService->getCurrentLanguage() === 'ua' ? 'description' : 'description_ru'],
                'value' => $item['ref']
            ];
        }, WCUSHelper::getDefaultCities());
    }

    private function getShipmentCost(): float
    {
        return $this->order->get_subtotal() + (float)$this->order->get_total_fees() + (float)$this->order->get_total_tax('') - $this->order->get_total_discount();
    }

    private function checkPoshtomatDelivery(string $warehouseRef): void
    {
        /** @var AddressProviderInterface $addressProvider */
        $addressProvider = wcus_container()->make(AddressProviderInterface::class);
        $warehouse = $addressProvider->searchWarehouseByRef($warehouseRef);
        if ($warehouse !== null) {
            if (false !== strpos($warehouse->getNameUa(), 'Поштомат') || false !== strpos($warehouse->getNameRu(), 'Почтомат')) {
                $this->data['ttn']['global_params'] = 0;
            }
        }
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
}
