<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Rates;

use kirillbdev\WCUkrShipping\Contracts\Rates\RatesCalculatorInterface;
use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

class OrderTotalRatesCalculator implements RatesCalculatorInterface
{
    private RatesCalculatorInterface $apiRatesCalculator;
    private array $rules;

    public function __construct(RatesCalculatorInterface $apiRatesCalculator, array $rules)
    {
        $this->apiRatesCalculator = $apiRatesCalculator;
        $this->rules = $rules;
    }

    public function calculateRates(OrderInfoDto $orderInfo): ?float
    {
        if (count($this->rules) === 0) {
            return null;
        }

        usort($this->rules, function ($a, $b) {
            return (float)$a['total'] <=> (float)$b['total'];
        });

        $shippingPrice = null;
        foreach ($this->rules as $rule) {
            if ($orderInfo->subTotal > (float)$rule['total']) {
                $shippingPrice = $rule['price'];
            }
        }

        return $shippingPrice === '[api]'
            ? $this->apiRatesCalculator->calculateRates($orderInfo)
            : (float)$shippingPrice;
    }
}
