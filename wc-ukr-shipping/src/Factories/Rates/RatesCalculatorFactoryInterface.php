<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates;

use kirillbdev\WCUkrShipping\Contracts\Rates\RatesCalculatorInterface;
use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

interface RatesCalculatorFactoryInterface
{
    public function getRatesCalculator(OrderInfoDto $orderInfo): RatesCalculatorInterface;
}
