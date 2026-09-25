<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates;

use kirillbdev\WCUkrShipping\Dto\Rates\RateShipmentDTO;
use kirillbdev\WCUkrShipping\Factories\ProductFactory;
use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUkrShipping\Model\OrderProduct;
use kirillbdev\WCUkrShipping\Services\Calculation\ProductDimensionService;

class CheckoutRateShipmentFactory
{
    protected string $carrierSlug;
    protected array $data;
    protected string $fieldGroup;
    protected bool $useDimensions;

    private ProductDimensionService $productDimensionService;

    public function __construct(string $carrierSlug, bool $useDimensions = true)
    {
        $this->carrierSlug = $carrierSlug;
        $this->useDimensions = $useDimensions;
        $this->productDimensionService = wcus_container()->make(ProductDimensionService::class);

        $data = WCUSHelper::getCheckoutPostData();
        $this->fieldGroup = WCUSHelper::getCheckoutFieldGroup($data);

        $data['ship_to'] = [
            'country' => $data[$this->fieldGroup . '_country'] ?? '',
            'city' => $data[$this->fieldGroup . '_city'] ?? null,
            'state' => $data[$this->fieldGroup . '_state'] ?? null,
            'address_1' => $data[$this->fieldGroup . '_address_1'] ?? null,
            'address_2' => $data[$this->fieldGroup . '_address_2'] ?? null,
            'postal_code' => $data[$this->fieldGroup . '_postcode'] ?? null,
        ];

        $this->data = $data;
    }

    public function createRateShipment(): RateShipmentDTO
    {
        $products = $this->getCartProducts();

        return new RateShipmentDTO(
            $this->carrierSlug,
            $this->get($this->fieldGroup . '_country', 'UA'),
            (float)wc()->cart->get_subtotal(),
            $this->productDimensionService->getTotalWeight($products),
            $this->get('payment_method', ''),
            $this->getDeliveryType(),
            $this->isFull(),
            $this->useDimensions ? $this->productDimensionService->getTotalDimensions($products) : null,
            $this->getShipToCarrierCityId(),
            $this->getShipToPUDOPointId(),
            $this->getServiceType(),
            $products,
            $this->getShipToCity(),
            $this->getShipToPostalCode(),
            $this->getShipToAddress1()
        );
    }

    protected function getDeliveryType(): string
    {
        return 'w2w';
    }

    protected function isFull(): bool
    {
        return true;
    }

    protected function getShipToCarrierCityId(): ?string
    {
        return null;
    }

    protected function getShipToPUDOPointId(): ?string
    {
        return null;
    }

    protected function getServiceType():?string
    {
        return null;
    }

    protected function getShipToCity():?string
    {
        return null;
    }

    protected function getShipToPostalCode():?string
    {
        return null;
    }

    protected function getShipToAddress1():?string
    {
        return null;
    }

    /**
     * @return OrderProduct[]
     */
    protected function getCartProducts(): array
    {
        $products = [];
        /** @var ProductFactory $factory */
        $factory = wcus_container()->make(ProductFactory::class);
        $items = wc()->cart->get_cart();

        foreach ($items as $item) {
            $product = $factory->makeCartItemProduct($item);

            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
}
