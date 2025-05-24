<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Validation;

class UkrposhtaCheckoutValidator implements CheckoutValidatorInterface
{
    public function validate(array $data): void
    {
        $this->validateWarehouseShipping($this->getTypeToValidate($data), $data);
    }

    private function getTypeToValidate(array $data): string
    {
        if (isset($data['ship_to_different_address']) && 1 === (int)$data['ship_to_different_address']) {
            return 'shipping';
        }

        return 'billing';
    }

    private function validateWarehouseShipping(string $type, array $data): void
    {
        if (empty($data['wcus_ukrposhta_' . $type . '_city'])
            || empty($data['wcus_ukrposhta_' . $type . '_warehouse'])
        ) {
            wc_add_notice(
                __('Select Ukrposhta warehouse', 'wc-ukr-shipping-i18n'),
                'error'
            );
        }
    }
}
