<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Validation;

class NovaPoshtaCheckoutValidator implements CheckoutValidatorInterface
{
    public function validate(array $data): void
    {
        $type = $this->getTypeToValidate($data);

        if ($this->maybeAddressShippingSelected($type, $data)) {
            $this->validateAddressShipping($type, $data);
        } else {
            $this->validateWarehouseShipping($type, $data);
        }
    }

    private function getTypeToValidate(array $data): string
    {
        if (isset($data['ship_to_different_address']) && 1 === (int)$data['ship_to_different_address']) {
            return 'shipping';
        }

        return 'billing';
    }

    private function maybeAddressShippingSelected(string $type, array $data): bool
    {
        return isset($data['wcus_np_' . $type . '_custom_address_active'])
            && 1 === (int)$data['wcus_np_' . $type . '_custom_address_active'];
    }

    private function validateAddressShipping(string $type, array $data): void
    {
        if ((int)wc_ukr_shipping_get_option('wc_ukr_shipping_np_address_api_ui') === 1) {
            if (empty($data['wcus_np_' . $type . '_settlement_name'])
                || empty($data['wcus_np_' . $type . '_street_name'])
                || empty($data['wcus_np_' . $type . '_house'])) {
                $this->addErrorNotice();
            }
        } else {
            if (empty($data['wcus_np_' . $type . '_city'])
                || empty($data['wcus_np_' . $type . '_custom_address'])
            ) {
                $this->addErrorNotice();
            }
        }
    }

    private function validateWarehouseShipping(string $type, array $data): void
    {
        if (empty($data['wcus_np_' . $type . '_city'])
            || empty($data['wcus_np_' . $type . '_warehouse'])
        ) {
            $this->addErrorNotice();
        }
    }

    private function addErrorNotice(): void
    {
        wc_add_notice(
            __('Enter shipping address of Nova Poshta', 'wc-ukr-shipping-i18n'),
            'error'
        );
    }
}
