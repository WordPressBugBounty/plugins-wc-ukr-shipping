<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Services;

use kirillbdev\WCUkrShipping\Api\SmartyParcelApi;
use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Component\SmartyParcel\LabelRequestBuilderInterface;
use kirillbdev\WCUkrShipping\Contracts\Cache\LockProviderInterface;
use kirillbdev\WCUkrShipping\DB\Repositories\ShippingLabelsRepository;
use kirillbdev\WCUkrShipping\Dto\SmartyParcel\Labels\CreateLabelResponseDto;

class SmartyParcelService
{
    private SmartyParcelApi $api;
    private ShippingLabelsRepository $labelsRepository;
    private LockProviderInterface $lockProvider;
    private AutomationService $automationService;

    private ?array $carrierAccounts = null;

    public function __construct(
        SmartyParcelApi $api,
        ShippingLabelsRepository $labelsRepository,
        AutomationService $automationService
    ) {
        $this->api = $api;
        $this->labelsRepository = $labelsRepository;
        $this->automationService = $automationService;
        $this->lockProvider = wcus_container()->make(LockProviderInterface::class);
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
        if (empty($accountCache)) {
            if (!$this->lockProvider->lock('smarty_parcel_account', 60)) {
                return null;
            }

            $apiKey = get_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY);
            if (empty($apiKey)) {
                $this->lockProvider->releaseLock('smarty_parcel_account');
                return null;
            }

            try {
                $accountCache = $this->api->getUserStatus($apiKey);
                set_transient('smarty_parcel_account', $accountCache, 900);
                $this->lockProvider->releaseLock('smarty_parcel_account');
            } catch (\Throwable $e) {
                $accountCache = null;
            }
        }

        return $accountCache;
    }

    public function hasPaidAccount(): bool
    {
        $account = $this->getAccountInfo();
        if ($account === null) {
            return false;
        }
        $isFree = $account['subscription_plan']['is_free'] ?? true;

        return !$isFree;
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

    public function createLabel(
        int $orderId,
        LabelRequestBuilderInterface $builder,
        bool $addTracking
    ): CreateLabelResponseDto {
        $order = wc_get_order($orderId);
        if ($order === null) {
            throw new \Exception("Order $orderId not found");
        }

        $response = $this->api->createLabel($builder);
        $this->labelsRepository->create(
            $orderId,
            $response['id'],
            $response['tracking_number'],
            $response['carrier_slug'] ?? 'nova_poshta'
        );
        $shippingLabel = $this->labelsRepository->findByOrderId($orderId);

        if ($addTracking) {
            try {
                $this->api->addTracking($response['tracking_number']);
                $this->labelsRepository->addToTracking((int)$shippingLabel['id']);
            } catch (\Throwable $e) {
                // todo: logs ?
            }
        }

        $this->automationService->executeEvent(
            AutomationService::EVENT_LABEL_CREATED,
            new Context(
                AutomationService::EVENT_LABEL_CREATED,
                $order,
                [
                    'tracking_number' => $response['tracking_number'],
                    'carrier_status' => ''
                ]
            )
        );

        return new CreateLabelResponseDto(
            (int)$shippingLabel['id'],
            $orderId,
            $response['id'],
            $response['tracking_number'],
            (float)$response['shipment_cost']['amount'],
            new \DateTimeImmutable($response['estimated_delivery_date']),
            $addTracking ? 'PENDING' : ''
        );
    }

    public function createOneTimeUpgradeAction(string $subscription): string
    {
        $response = $this->api->createSubscriptionChangeAction($subscription);
        return $response['one_time_token'];
    }
}
