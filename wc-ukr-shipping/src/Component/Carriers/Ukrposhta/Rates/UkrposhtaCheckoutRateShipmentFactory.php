<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Carriers\Ukrposhta\Rates;

use kirillbdev\WCUkrShipping\Enums\CarrierSlug;
use kirillbdev\WCUkrShipping\Factories\Rates\CheckoutRateShipmentFactory;

class UkrposhtaCheckoutRateShipmentFactory extends CheckoutRateShipmentFactory
{
    private string $serviceType;
    private string $deliveryType;

    public function __construct(string $serviceType, string $deliveryType, bool $useDimensions)
    {
        parent::__construct(CarrierSlug::UKRPOSHTA, $useDimensions);
        $this->serviceType = $serviceType;
        $this->deliveryType = $deliveryType;
    }

    protected function isFull(): bool
    {
        if ($this->deliveryType === 'door') {
            return !empty($this->get('ship_to')['postal_code'] ?? null);
        }

        // A point picked on the map leaves the city ref empty - Ukrposhta
        // returns none on geo lookups - so the point alone has to be enough
        return !empty($this->getShipToPUDOPointId())
            || !empty($this->getShipToCarrierCityId());
    }

    protected function getShipToCarrierCityId(): ?string
    {
        return $this->get("wcus_ukrposhta_{$this->fieldGroup}_city", '');
    }

    /**
     * Preferred over the city ref by the calculator, and the only thing left
     * once a point comes from the map. A city chosen without a warehouse still
     * falls back to the ref, so rates keep appearing as early as they did.
     */
    protected function getShipToPUDOPointId(): ?string
    {
        if ($this->deliveryType === 'door') {
            return null;
        }

        $warehouse = (string)$this->get("wcus_ukrposhta_{$this->fieldGroup}_warehouse", '');

        return $warehouse === '' ? null : $warehouse;
    }

    protected function getShipToPostalCode(): ?string
    {
        return $this->deliveryType === 'door'
            ? ($this->get('ship_to')['postal_code'] ?? null)
            : null;
    }

    protected function getServiceType(): ?string
    {
        return 'ukrposhta_' . $this->serviceType;
    }

    protected function getDeliveryType(): string
    {
        return $this->deliveryType === 'door' ? 'w2d' : 'w2w';
    }
}
