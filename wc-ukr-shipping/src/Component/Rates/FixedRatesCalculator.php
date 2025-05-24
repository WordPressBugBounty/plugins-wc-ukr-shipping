<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Rates;

use kirillbdev\WCUkrShipping\Contracts\Rates\RatesCalculatorInterface;
use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

class FixedRatesCalculator implements RatesCalculatorInterface
{
    private float $fixedRate;

    public function __construct(float $fixedRate)
    {
        $this->fixedRate = $fixedRate;
    }

    public function calculateRates(OrderInfoDto $orderInfo): ?float
    {
        return $this->fixedRate;
    }
}
