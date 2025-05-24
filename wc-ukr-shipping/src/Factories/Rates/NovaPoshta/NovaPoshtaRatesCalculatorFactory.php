<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates\NovaPoshta;

use kirillbdev\WCUkrShipping\Component\Rates\ApiRatesCalculator;
use kirillbdev\WCUkrShipping\Component\Rates\FixedRatesCalculator;
use kirillbdev\WCUkrShipping\Component\Rates\OrderTotalRatesCalculator;
use kirillbdev\WCUkrShipping\Contracts\Rates\RatesCalculatorInterface;
use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;
use kirillbdev\WCUkrShipping\Factories\Rates\RatesCalculatorFactoryInterface;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;

class NovaPoshtaRatesCalculatorFactory implements RatesCalculatorFactoryInterface
{
    private SmartyParcelService $smartyParcelService;

    public function __construct(SmartyParcelService $smartyParcelService)
    {
        $this->smartyParcelService = $smartyParcelService;
    }

    public function getRatesCalculator(OrderInfoDto $orderInfo): RatesCalculatorInterface
    {
        $calcType = wc_ukr_shipping_get_option('wc_ukr_shipping_np_price_type');
        $apiCalculator = new ApiRatesCalculator(
            $this->smartyParcelService,
            (string)wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_city'),
            (string)get_option('wcus_nova_poshta_default_carrier'),
            (int)wc_ukr_shipping_get_option('wcus_cod_payment_active') === 1
            && wc_ukr_shipping_get_option('wcus_cod_payment_id') === $orderInfo->paymentMethod
        );

        switch ($calcType) {
            case 'fixed':
                return new FixedRatesCalculator((float)wc_ukr_shipping_get_option('wc_ukr_shipping_np_price'));
            case 'calculate':
                return $apiCalculator;
            case 'relative_to_total':
                $rules = wc_ukr_shipping_get_option('wc_ukr_shipping_np_relative_price');
                if ($rules) {
                    $rules = json_decode($rules, true);
                }
                return new OrderTotalRatesCalculator($apiCalculator, json_last_error() ? [] : $rules);
            default:
                throw new \InvalidArgumentException('Unsupported calculation type ' . $calcType);
        }
    }
}
