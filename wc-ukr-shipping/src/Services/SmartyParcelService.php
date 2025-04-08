<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Services;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;

class SmartyParcelService
{
    private SmartyParcelApi $api;

    private ?array $carrierAccounts = null;

    public function __construct(SmartyParcelApi $api)
    {
        $this->api = $api;
    }

    public function getCarrierAccounts(): array
    {
        if ($this->carrierAccounts === null) {
            $carrierAccounts = get_option(WCUS_OPTION_SMARTY_PARCEL_CARRIERS);
            if ($carrierAccounts) {
                $carrierAccounts = json_decode($carrierAccounts, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($carrierAccounts)) {
                    $this->carrierAccounts = $carrierAccounts;
                }
            }
        }

        return $this->carrierAccounts ?? [];
    }

    public function getAccountInfo(): ?array
    {
        $accountCache = get_transient('smarty_parcel_account');
        if ($accountCache === false) {
            $apiKey = get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
            if (empty($apiKey)) {
                return null;
            }

            try {
                $accountCache = $this->api->getUserStatus($apiKey);
                set_transient('smarty_parcel_account', $accountCache, 3600);
            } catch (\Throwable $e) {
                $this->accountCache = null;
            }
        }

        return $accountCache;
    }

    public function getRates(
        string $shipTo,
        string $deliveryType,
        float $declaredValue,
        float $weight
    ): ?array {
        $apiKey = get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
        if (empty($apiKey)) {
            return null;
        }

        $carrierAccount = get_option('wcus_nova_poshta_default_carrier');
        if (empty($carrierAccount)) {
            return null;
        }

        return $this->api->estimateRates(
            $carrierAccount,
            wc_ukr_shipping_get_option('wc_ukr_shipping_np_sender_city'),
            $shipTo,
            $deliveryType,
            $declaredValue,
            $weight
        );
    }
}
