<?php

namespace kirillbdev\WCUkrShipping\Model;

use kirillbdev\WCUkrShipping\Contracts\AddressInterface;
use kirillbdev\WCUkrShipping\Contracts\Customer\CustomerStorageInterface;
use kirillbdev\WCUkrShipping\Contracts\OrderDataInterface;

if ( ! defined('ABSPATH')) {
    exit;
}

class OrderShipping
{
    /**
     * @var \WC_Order_Item_Shipping
     */
    private $item;

    /**
     * @var CustomerStorageInterface
     */
    private $customerStorage;

    /**
     * @param \WC_Order_Item_Shipping $item
     */
    public function __construct($item)
    {
        $this->item = $item;
        $this->customerStorage = wcus_container()->make(CustomerStorageInterface::class);
    }

    /**
     * @param OrderDataInterface $data
     */
    public function save($data)
    {
        $this->customerStorage->remove(CustomerStorageInterface::KEY_LAST_CITY_REF);
        $this->customerStorage->remove(CustomerStorageInterface::KEY_LAST_WAREHOUSE_REF);
        $address = $data->getShippingAddress();

        if ($data->isAddressShipping()) {
            $this->saveAddressShipping($address);
        } else {
            $this->saveWarehouseShipping($address);
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function updateMeta($key, $value)
    {
        $this->item->update_meta_data($key, sanitize_text_field(wp_unslash($value)));
    }

    /**
     * @param AddressInterface $address
     */
    private function saveAddressShipping($address)
    {
        if ((int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_api_ui') === 1 || is_admin()) {
            $this->item->add_meta_data('wcus_settlement_ref', $this->sanitizeValue($address->getSettlementInfo('ref')));
            $this->item->add_meta_data('wcus_settlement_full', $this->sanitizeValue($address->getSettlementInfo('full')));
            $this->item->add_meta_data('wcus_settlement_name', $this->sanitizeValue($address->getSettlementInfo('name')));
            $this->item->add_meta_data('wcus_settlement_area', $this->sanitizeValue($address->getSettlementInfo('area')));
            $this->item->add_meta_data('wcus_settlement_region', $this->sanitizeValue($address->getSettlementInfo('region')));

            $this->item->add_meta_data('wcus_street_ref', $this->sanitizeValue($address->getStreetInfo('ref')));
            $this->item->add_meta_data('wcus_street_name', $this->sanitizeValue($address->getStreetInfo('name')));
            $this->item->add_meta_data('wcus_street_full', $this->sanitizeValue($address->getStreetInfo('full')));
            $this->item->add_meta_data('wcus_house', $this->sanitizeValue($address->getHouse()));
            $this->item->add_meta_data('wcus_flat', $this->sanitizeValue($address->getFlat()));
            $this->item->add_meta_data('wcus_api_address', 1);

            // fixme: This logic may be attached through event model
            $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_SETTLEMENT, [
                'full' => $this->sanitizeValue($address->getSettlementInfo('full')),
                'ref' => $this->sanitizeValue($address->getSettlementInfo('ref')),
                'name' => $this->sanitizeValue($address->getSettlementInfo('name')),
                'area' => $this->sanitizeValue($address->getSettlementInfo('area')),
                'region' => $this->sanitizeValue($address->getSettlementInfo('region'))
            ]);
            $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_STREET, [
                'full' => $this->sanitizeValue($address->getStreetInfo('full')),
                'ref' => $this->sanitizeValue($address->getStreetInfo('ref')),
                'name' => $this->sanitizeValue($address->getStreetInfo('name'))
            ]);
            $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_HOUSE, $this->sanitizeValue($address->getHouse()));
            $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_FLAT, $this->sanitizeValue($address->getFlat()));
        } else {
            if (!$this->isNewUiEnabled()) {
                $this->updateMeta('wcus_area_ref', $address->getAreaRef());
            }
            $this->updateMeta('wcus_city_ref', $address->getCityRef());
            $this->updateMeta('wcus_address', $address->getCustomAddress());
            $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_CITY_REF, sanitize_text_field($address->getCityRef()));
        }
    }

    /**
     * @param AddressInterface $address
     */
    private function saveWarehouseShipping($address)
    {
        if (!$this->isNewUiEnabled()) {
            $this->updateMeta('wcus_area_ref', $address->getAreaRef());
        }
        $this->updateMeta('wcus_city_ref', $address->getCityRef());
        $this->updateMeta('wcus_warehouse_ref', $address->getWarehouseRef());
        $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_CITY_REF, sanitize_text_field($address->getCityRef()));
        $this->customerStorage->add(CustomerStorageInterface::KEY_LAST_WAREHOUSE_REF, sanitize_text_field($address->getWarehouseRef()));
    }

    private function isNewUiEnabled(): bool
    {
        return (int)get_option('wcus_checkout_new_ui') === 1;
    }

    private function sanitizeValue(string $value): string
    {
        return sanitize_text_field(wp_unslash($value));
    }
}