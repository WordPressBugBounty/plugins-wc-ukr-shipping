<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Contracts\Rates;

use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

interface RatesCalculatorInterface
{
    public function calculateRates(OrderInfoDto $orderInfo): ?float;
}
