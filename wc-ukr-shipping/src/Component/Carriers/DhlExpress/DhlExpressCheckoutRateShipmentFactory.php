<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Carriers\DhlExpress;

use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Factories\Rates\CheckoutRateShipmentFactory;

if ( ! defined('ABSPATH')) {
    exit;
}

class DhlExpressCheckoutRateShipmentFactory extends CheckoutRateShipmentFactory
{
    private string $serviceType;

    public function __construct(string $serviceType)
    {
        parent::__construct(CarrierSlug::DHL_EXPRESS);
        $this->serviceType = $serviceType;
    }

    protected function getServiceType(): ?string
    {
        return $this->serviceType !== '' ? $this->serviceType : null;
    }

    protected function getDeliveryType(): string
    {
        return 'w2d';
    }

    protected function getShipToCity(): ?string
    {
        return $this->get('ship_to')['city'] ?: null;
    }

    protected function getShipToPostalCode(): ?string
    {
        return $this->get('ship_to')['postal_code'] ?: null;
    }

    protected function getShipToAddress1(): ?string
    {
        return $this->get('ship_to')['address_1'] ?: null;
    }

    protected function isFull(): bool
    {
        // DHL requires both country and city to estimate rates
        return !empty($this->get('ship_to')['country']) && !empty($this->getShipToCity());
    }
}
