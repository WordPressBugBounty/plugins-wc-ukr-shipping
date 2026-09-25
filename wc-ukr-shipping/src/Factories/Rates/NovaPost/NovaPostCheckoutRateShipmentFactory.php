<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates\NovaPost;

use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Factories\Rates\CheckoutRateShipmentFactory;

class NovaPostCheckoutRateShipmentFactory extends CheckoutRateShipmentFactory
{
    private string $deliveryType;

    public function __construct(string $deliveryType = 'warehouse')
    {
        parent::__construct(CarrierSlug::NOVA_POST);
        $this->deliveryType = $deliveryType;
    }

    protected function getShipToPUDOPointId(): ?string
    {
        return $this->deliveryType === 'warehouse'
            ? $this->get("wcus_nova_post_{$this->fieldGroup}_warehouse")
            : null;
    }

    protected function getShipToCity(): ?string
    {
        return $this->deliveryType === 'door'
            ? $this->get('ship_to')['city'] ?? null
            : null;
    }

    protected function getShipToAddress1(): ?string
    {
        return $this->deliveryType === 'door'
            ? $this->get('ship_to')['address_1'] ?? null
            : null;
    }

    protected function getShipToPostalCode(): ?string
    {
        return $this->deliveryType === 'door'
            ? $this->get('ship_to')['postal_code'] ?? null
            : null;
    }

    protected function isFull(): bool
    {
        if ($this->deliveryType === 'door') {
            return !empty($this->getShipToCity())
                && !empty($this->getShipToPostalCode())
                && !empty($this->getShipToAddress1());
        }
        return !empty($this->getShipToPUDOPointId());
    }
}
