<?php

namespace kirillbdev\WCUkrShipping\Services;

use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;
use kirillbdev\WCUkrShipping\Factories\Rates\NovaPoshta\NovaPoshtaRatesCalculatorFactory;
use kirillbdev\WCUkrShipping\Factories\Rates\RatesCalculatorFactoryInterface;
use kirillbdev\WCUkrShipping\Factories\Rates\Ukrposhta\UkrposhtaRatesCalculatorFactory;

if ( ! defined('ABSPATH')) {
    exit;
}

class CalculationService
{
    private SmartyParcelService $smartyParcelService;

    public function __construct()
    {
        $this->smartyParcelService = wcus_container()->make(SmartyParcelService::class);
    }

    public function calculateRates(OrderInfoDto $orderInfo): ?float
    {
        $factory = $this->getRatesCalculatorFactory($orderInfo->shippingMethod);

        // todo: apply_filters('wcus_calculate_shipping_cost', $cost, $orderData)

        return $factory->getRatesCalculator($orderInfo)->calculateRates($orderInfo);
    }

    private function getRatesCalculatorFactory(string $shippingMethod): RatesCalculatorFactoryInterface
    {
        if ($shippingMethod === WC_UKR_SHIPPING_NP_SHIPPING_NAME) {
            return new NovaPoshtaRatesCalculatorFactory($this->smartyParcelService);
        } elseif ($shippingMethod === WCUS_SHIPPING_METHOD_UKRPOSHTA) {
            return new UkrposhtaRatesCalculatorFactory($this->smartyParcelService);
        }

        throw new \InvalidArgumentException('Invalid shipping method: ' . sanitize_text_field($shippingMethod));
    }
}
