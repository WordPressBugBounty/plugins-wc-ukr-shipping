<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Dto\Shipping;

final class SearchPUDOByGeoRequestDTO
{
    public float $lat;
    public float $lng;
    /**
     * Search radius in kilometers - a provider converts it to whatever unit
     * its own API speaks.
     */
    public float $radius;
    public string $countryCode;
    public array $types;
    public ?float $weight;

    public function __construct(
        float $lat,
        float $lng,
        float $radius,
        string $countryCode,
        array $types,
        ?float $weight = null
    ) {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
        $this->countryCode = $countryCode;
        $this->types = $types;
        $this->weight = $weight;
    }
}
