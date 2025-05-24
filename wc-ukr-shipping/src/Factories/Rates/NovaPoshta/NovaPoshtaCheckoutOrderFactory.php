<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Factories\Rates\NovaPoshta;

use kirillbdev\WCUkrShipping\Dto\Rates\OrderInfoDto;

class NovaPoshtaCheckoutOrderFactory
{
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
            WC_UKR_SHIPPING_NP_SHIPPING_NAME,
            $data['payment_method'] ?? '',
            (float)wc()->cart->get_subtotal(),
            $weight > 0 ? $weight : 0.1,
            $this->getDeliveryType($data)
        );
    }

    private function getShipToCity(array $data): string
    {
        $group = isset($data['ship_to_different_address']) && (int)$data['ship_to_different_address'] === 1
            ? 'shipping'
            : 'billing';

        if ($data['shipping_type'] === 'doors' && (int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_api_ui') === 1) {
            return $data["wcus_np_{$group}_settlement_ref"] ?? '';
        }

        return $data["wcus_np_{$group}_city"] ?? '';
    }

    private function getDeliveryType(array $data): string
    {
        switch ($data['shipping_type']) {
            case 'warehouse':
                return 'w2w';
            case 'doors':
                return 'w2d';
            case 'poshtomat':
                return 'w2l';
            default:
                throw new \InvalidArgumentException("Unsupported delivery type");
        }
    }
}
