<?php

namespace kirillbdev\WCUkrShipping\Model;

use kirillbdev\WCUkrShipping\Contracts\AddressInterface;
use kirillbdev\WCUkrShipping\Contracts\OrderDataInterface;
use kirillbdev\WCUkrShipping\DB\NovaPoshtaRepository;
use kirillbdev\WCUkrShipping\DB\Repositories\AreaRepositoryInterface;
use kirillbdev\WCUkrShipping\Services\TranslateService;

if ( ! defined('ABSPATH')) {
    exit;
}

class WCUSOrder
{
    /**
     * @var \WC_Order
     */
    private $wcOrder;

    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;

    /**
     * @var NovaPoshtaRepository
     */
    private $repository;

    /**
     * @var TranslateService
     */
    private $translateService;

    /**
     * @param \WC_Order $wcOrder
     */
    public function __construct($wcOrder)
    {
        $this->wcOrder = $wcOrder;
        $this->repository = new NovaPoshtaRepository();
        $this->translateService = new TranslateService();
        $this->areaRepository = wcus_container()->make(AreaRepositoryInterface::class);
    }

    /**
     * @param OrderDataInterface $data
     *
     * @throws \WC_Data_Exception
     */
    public function save($data)
    {
        if ( ! $data->isShipToDifferentAddress()) {
            $this->wcOrder->set_shipping_first_name($this->wcOrder->get_billing_first_name());
            $this->wcOrder->set_shipping_last_name($this->wcOrder->get_billing_last_name());
        }

        $address = $data->getShippingAddress();

        if ($data->isAddressShipping()) {
            $this->saveAddressShipping($address);
        } else {
            $this->saveWarehouseShipping($address);
        }

        $this->wcOrder->update_meta_data('wcus_data_version', '2');
    }

    /**
     * @param string $state
     *
     * @throws \WC_Data_Exception
     */
    public function setState($state)
    {
        $state = sanitize_text_field(wp_unslash($state));
        if ('billing_only' === get_option('woocommerce_ship_to_destination')) {
            $this->wcOrder->set_billing_state($state);
        }
        else {
            $this->wcOrder->set_shipping_state($state);
        }
    }

    /**
     * @param string $city
     *
     * @throws \WC_Data_Exception
     */
    public function setCity($city)
    {
        $city = sanitize_text_field(wp_unslash($city));
        if ('billing_only' === get_option('woocommerce_ship_to_destination')) {
            $this->wcOrder->set_billing_city($city);
        }
        else {
            $this->wcOrder->set_shipping_city($city);
        }
    }

    /**
     * @param string $address
     *
     * @throws \WC_Data_Exception
     */
    public function setAddress($address)
    {
        $address = sanitize_text_field(wp_unslash($address));
        if ('billing_only' === get_option('woocommerce_ship_to_destination')) {
            $this->wcOrder->set_billing_address_1($address);
        }
        else {
            $this->wcOrder->set_shipping_address_1($address);
        }
    }

    /**
     * @param AddressInterface $address
     */
    private function saveAddressShipping($address)
    {
        $this->saveArea($address);
        $this->saveCity($address);
        $this->setAddress(sanitize_text_field($address->getCustomAddress()));
    }

    /**
     * @param AddressInterface $address
     */
    private function saveWarehouseShipping($address)
    {
        $this->saveArea($address);
        $this->saveCity($address);
        $this->saveWarehouse($address);
    }

    private function saveArea(AddressInterface $address)
    {
        if ($this->isNewUiEnabled()) {
            $city = $this->repository->getCityByRef($address->getCityRef());
            if ($city) {
                $area = $this->areaRepository->findByRef($city['area_ref']);
                if ($area !== null) {
                    $this->setState(
                        $this->translateService->getCurrentLanguage() === 'ua' ? $area->getNameUa() : $area->getNameRu()
                    );
                }
            }
        } else {
            $area = $this->repository->getAreaByRef($address->getAreaRef());

            if ($area) {
                $this->setState(sanitize_text_field($this->translateService->translateArea($area)));
            }
        }
    }

    private function saveCity(AddressInterface $address)
    {
        if ($this->isNewUiEnabled() && $address->getCityName() !== null) {
            $this->setCity(sanitize_text_field($address->getCityName()));
        } else {
            $city = $this->repository->getCityByRef($address->getCityRef());
            if ($city) {
                $this->setCity(sanitize_text_field($this->translateService->translateCity($city)));
            }
        }
    }

    private function saveWarehouse(AddressInterface $address): void
    {
        if ($this->isNewUiEnabled() && $address->getWarehouseName() !== null) {
            $this->setAddress(sanitize_text_field($address->getWarehouseName()));
        } else {
            $warehouse = $this->repository->getWarehouseByRef($address->getWarehouseRef());

            if ($warehouse) {
                $this->setAddress(sanitize_text_field($this->translateService->translateWarehouse($warehouse)));
            }
        }
    }

    private function isNewUiEnabled(): bool
    {
        return (int)get_option('wcus_checkout_new_ui') === 1;
    }
}