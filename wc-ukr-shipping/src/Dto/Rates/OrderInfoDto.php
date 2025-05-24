<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Dto\Rates;

if ( ! defined('ABSPATH')) {
    exit;
}

final class OrderInfoDto
{
    public string $shipToCityId;
    public string $shippingMethod;
    public string $paymentMethod;
    public float $subTotal;
    public float $weight;
    public string $deliveryType;
    public ?string $shippingServiceType;
    public array $products;
    public array $meta;

    public function __construct(
        string $shipToCityId,
        string $shippingMethod,
        string $paymentMethod,
        float $subTotal,
        float $weight,
        string $deliveryType,
        ?string $shippingServiceType = null,
        array $products = [],
        array $meta = []
    ) {
        $this->shipToCityId = $shipToCityId;
        $this->shippingMethod = $shippingMethod;
        $this->paymentMethod = $paymentMethod;
        $this->subTotal = $subTotal;
        $this->weight = $weight;
        $this->deliveryType = $deliveryType;
        $this->shippingServiceType = $shippingServiceType;
        $this->products = $products;
        $this->meta = $meta;
    }
}
