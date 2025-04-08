<?php

namespace kirillbdev\WCUkrShipping\Services;

use kirillbdev\WCUkrShipping\Contracts\OrderDataInterface;
use kirillbdev\WCUkrShipping\Exceptions\ApiServiceException;
use kirillbdev\WCUkrShipping\Model\OrderProduct;
use kirillbdev\WCUkrShipping\Services\Calculation\ProductDimensionService;

if ( ! defined('ABSPATH')) {
    exit;
}

class CalculationService
{
    /**
     * Cache result of calculation.
     */
    private static ?float $cost = null;

    private ProductDimensionService $productDimensionService;
    private SmartyParcelService $smartyParcelService;

    public function __construct()
    {
        $this->productDimensionService = wcus_container()->make(ProductDimensionService::class);
        $this->smartyParcelService = wcus_container()->make(SmartyParcelService::class);
    }

    public function calculateCost(OrderDataInterface $orderData): ?float
    {
        if (null !== self::$cost) {
            return self::$cost;
        }

       /* if ($orderData->isAddressShipping()) {
            $cost = $this->calculateAddressShipping($orderData);
        } else {
            $cost = $this->calculateWarehouseShipping($orderData);
        }*/

        $cost = $this->calculateWarehouseShipping($orderData);

        if ($cost !== null) {
            $cost = (float)apply_filters('wcus_calculate_shipping_cost', $cost, $orderData);
            self::$cost = $cost;
        }

        return $cost;
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return int|float
     */
    private function calculateWarehouseShipping($orderData)
    {
        $calcType = wc_ukr_shipping_get_option('wc_ukr_shipping_np_price_type');

        if ('calculate' === $calcType) {
            return $this->calculateCostFromApi($orderData);
        } elseif ('relative_to_total' === $calcType) {
            return $this->calculateTotalRelativeCost($orderData);
        } elseif ('fixed' === $calcType) {
            return (float)wc_ukr_shipping_get_option('wc_ukr_shipping_np_price');
        }

        return 0;
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return int|float
     */
    private function calculateAddressShipping($orderData)
    {
        if ( ! (int)wcus_get_option('address_calc_enable')) {
            return $this->calculateWarehouseShipping($orderData);
        }

        if ('fixed' === wcus_get_option('address_calc_type')) {
            return (float)wcus_get_option('address_fixed_cost');
        } elseif ('total_relative' === wcus_get_option('address_calc_type')) {
            return $this->calculateAddressTotalShipping($orderData);
        }

        return 0;
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return int|float|null
     */
    private function calculateCostFromApi($orderData)
    {
        $weight = 0;
        $total = $orderData->getCalculatedTotal();
        $address = $orderData->getShippingAddress();
        $seats = $this->getSeats($orderData->getProducts());

        foreach ($seats as $seat) {
            $weight += $seat['weight'];
        }

        if (!$address->getCityRef()) {
            return null;
        }

        try {
            $deliveryType = $orderData->isAddressShipping() ? 'w2d' : 'w2w';
            if ($orderData->getShippingType() !== null) {
                $deliveryType = $this->convertShippingType($orderData->getShippingType());
            }

            $response = $this->smartyParcelService->getRates(
                $address->getCityRef(),
                $deliveryType,
                $total,
                $weight
            );

            if ($response === null) {
                return null;
            }

            $cost = 0;
            $rates = $response['data'][0];
            foreach ($rates['detailed_charges'] ?? [] as $rate) {
                if ($rate['type'] === 'base') {
                    $cost += (float)$rate['amount'];
                } elseif ($rate['type'] === 'cod') {
                    if ((int)wc_ukr_shipping_get_option('wcus_cod_payment_active')
                        && wc_ukr_shipping_get_option('wcus_cod_payment_id') === $orderData->getPaymentMethod()) {
                        $cost += (float)$rate['amount'];
                    }
                }
            }

            return $cost === 0 ? null : $cost;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return float
     */
    private function calculateTotalRelativeCost($orderData)
    {
        $relativePrices = wc_ukr_shipping_get_option('wc_ukr_shipping_np_relative_price');
        $cost = 0;

        if ( ! $relativePrices) {
            $relativePrices = [
                ['total' => 0, 'price' => wc_ukr_shipping_get_option('wc_ukr_shipping_np_price')]
            ];
        } else {
            $relativePrices = json_decode($relativePrices, true);
        }

        $relativePrice = $this->findRelativeCost($orderData->getCalculatedTotal(), $relativePrices);

        if ('[api]' === $relativePrice['price']) {
            $cost = $this->calculateCostFromApi($orderData);
        }
        else {
            $cost = $relativePrice['price'];
        }

        return $cost;
    }

    /**
     * @param OrderDataInterface $orderData
     *
     * @return float
     */
    private function calculateAddressTotalShipping($orderData)
    {
        $cost = 0;
        $totalRelativeCost = wcus_get_option('address_total_cost');

        if ($totalRelativeCost) {
            $totalRelativeCost = json_decode($totalRelativeCost, true);
        }
        else {
            $totalRelativeCost = [
                [ 'total' => 0, 'price' => 50 ]
            ];
        }

        $relativePrice = $this->findRelativeCost($orderData->getCalculatedTotal(), $totalRelativeCost);

        if ('[api]' === $relativePrice['price']) {
            $cost = $this->calculateCostFromApi($orderData);
        }
        else {
            $cost = $relativePrice['price'];
        }

        return $cost;
    }

    /**
     * @param float $total
     * @param array $costs
     * @return array
     */
    private function findRelativeCost($total, $costs)
    {
        $result = $costs[0];

        foreach ($costs as $cost) {
            if ($total >= $cost['total']) {
                $result = $cost;
            }
        }

        return $result;
    }

    private function convertShippingType(string $shippingType): string
    {
        switch ($shippingType) {
            case 'warehouse':
                return 'w2w';
            case 'doors':
                return 'w2d';
            case 'poshtomat':
                return 'w2l';
            default:
                throw new \InvalidArgumentException("Unsupported delivery type '$shippingType'");
        }
    }

    /**
     * @param OrderProduct[] $products
     * @return mixed[]
     */
    private function getSeats(array $products): array
    {
        $seats = [];

        foreach ($products as $product) {
            for ($i = 0; $i < $product->getQuantity(); $i++) {
                $weight = $product->getWeight() > 0 ? $product->getWeight() : 0.1;
                $width = $height = $length = 1;

                if ($product->getWidth() > 0 && $product->getHeight() > 0 && $product->getLength() > 0) {
                    $width = $product->getWidth();
                    $height = $product->getHeight();
                    $length = $product->getLength();
                }

                $seats[] = [
                    'weight' => $weight,
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    'cost' => (float)$product->getOriginalProduct()->get_price(''),
                ];
            }
        }

        return $seats;
    }
}