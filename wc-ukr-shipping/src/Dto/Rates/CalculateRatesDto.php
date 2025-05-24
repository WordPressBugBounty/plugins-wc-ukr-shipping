<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Dto\Rates;

if ( ! defined('ABSPATH')) {
    exit;
}

class CalculateRatesDto
{
    public string $shipToCityId;
    public string $deliveryType;
    public float $declaredPrice;
    public float $weight;
    public ?string $serviceType;

    public function __construct(
        string $shipToCityId,
        string $deliveryType,
        float $declaredPrice,
        float $weight,
        ?string $serviceType = null
    ) {
        $this->shipToCityId = $shipToCityId;
        $this->deliveryType = $deliveryType;
        $this->declaredPrice = $declaredPrice;
        $this->weight = $weight;
        $this->serviceType = $serviceType;
    }
}
