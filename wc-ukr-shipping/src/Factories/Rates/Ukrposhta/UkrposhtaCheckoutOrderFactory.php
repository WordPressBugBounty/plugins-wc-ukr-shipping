<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates\Ukrposhta;

use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

class UkrposhtaCheckoutOrderFactory
{
    private string $serviceType;

    public function __construct(string $serviceType)
    {
        $this->serviceType = $serviceType;
    }

    public function createOrderInfo(): OrderInfoDto
    {
        if ($_GET['wc-ajax'] === 'update_order_review') {
            parse_str(sanitize_text_field($_POST['post_data']), $data);
        } elseif ($_GET['wc-ajax'] === 'checkout') {
            $data = $_POST;
        } else {
            throw new \InvalidArgumentException('No data provided');
        }

        $shipToCity = $this->getShipToCity($data);
        $weight = wc()->cart->get_cart_contents_weight();

        return new OrderInfoDto(
            $shipToCity,
            'wcus_ukrposhta_shipping',
            $data['payment_method'] ?? '',
            (float)wc()->cart->get_subtotal(),
            $weight > 0 ? $weight : 1,
            'w2w',
            $this->mapServiceType($this->serviceType)
        );
    }

    private function getShipToCity(array $data): string
    {
        $group = isset($data['ship_to_different_address']) && (int)$data['ship_to_different_address'] === 1
            ? 'shipping'
            : 'billing';

        return $data["wcus_ukrposhta_{$group}_city"] ?? '';
    }

    private function mapServiceType(string $serviceType): string
    {
        return 'ukrposhta_' . strtolower($serviceType);
    }
}
