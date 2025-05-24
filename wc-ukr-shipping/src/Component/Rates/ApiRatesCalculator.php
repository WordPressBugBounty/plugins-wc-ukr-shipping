<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Rates;

use kirillbdev\WCUkrShipping\Contracts\Rates\RatesCalculatorInterface;
use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;

class ApiRatesCalculator implements RatesCalculatorInterface
{
    private SmartyParcelService $smartyParcelService;
    private string $shipFromCityId;
    private string $carrierAccountId;
    private bool $includeCod;

    public function __construct(
        SmartyParcelService $smartyParcelService,
        string $shipFromCityId,
        string $carrierAccountId,
        bool $includeCod
    ) {
        $this->smartyParcelService = $smartyParcelService;
        $this->shipFromCityId = $shipFromCityId;
        $this->carrierAccountId = $carrierAccountId;
        $this->includeCod = $includeCod;
    }

    public function calculateRates(OrderInfoDto $orderInfo): ?float
    {
        if (empty($this->shipFromCityId) || empty($orderInfo->shipToCityId)) {
            return null;
        }

        try {
            $response = $this->smartyParcelService->getRates(
                $this->carrierAccountId,
                $this->shipFromCityId,
                $orderInfo->shipToCityId,
                $orderInfo->deliveryType,
                $orderInfo->subTotal,
                $orderInfo->weight,
                $orderInfo->shippingServiceType
            );

            if ($response === null) {
                return null;
            }

            $cost = 0;
            $rates = $response['data'][0];
            foreach ($rates['detailed_charges'] ?? [] as $rate) {
                if ($rate['type'] === 'base') {
                    $cost += (float)$rate['amount'];
                } elseif ($rate['type'] === 'cod' && $this->includeCod) {
                    $cost += (float)$rate['amount'];
                }
            }

            return $cost === 0 ? null : $cost;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
