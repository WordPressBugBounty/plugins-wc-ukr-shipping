<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Services\SmartyParcel;

use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;
use kirillbdev\WCUkrShipping\Helpers\SmartyParcelHelper;
use kirillbdev\WCUkrShipping\Services\CarrierService;

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * The help assistant of SmartyParcel: its key (one per site, kept on the server, never sent to the browser)
 * and the short-lived chat tokens issued for it.
 */
class AssistantService
{
    /**
     * A cached token is renewed when less than this is left. Must stay above the margin of the widget (60s),
     * otherwise the widget treats a fresh token from the cache as expiring and asks for a new one on every call.
     */
    private const TOKEN_REFRESH_MARGIN = 300;

    private const ERROR_UNAUTHORIZED = 1501;

    private SmartyParcelWPApi $api;
    private CarrierService $carrierService;

    public function __construct(SmartyParcelWPApi $api, CarrierService $carrierService)
    {
        $this->api = $api;
        $this->carrierService = $carrierService;
    }

    public function hasConsent(): bool
    {
        return $this->getKey() !== '';
    }

    /**
     * The user accepted the consent text: the site gets its assistant key.
     */
    public function connect(string $consentVersion): void
    {
        if ($this->hasConsent()) {
            return;
        }

        $issued = $this->api->assistantConnect($consentVersion);
        if (empty($issued['key'])) {
            throw new \RuntimeException('Malformed assistant connect response');
        }

        update_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_KEY, (string)$issued['key'], false);
        delete_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_TOKEN);
    }

    /**
     * @return array{token: string, expires_at: string}
     * @throws SmartyParcelErrorException
     */
    public function getToken(): array
    {
        $key = $this->getKey();
        if ($key === '') {
            throw new \RuntimeException('The assistant is not connected');
        }

        $cached = get_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_TOKEN);
        if (
            is_array($cached)
            && ! empty($cached['token'])
            && $this->expiresIn((string)($cached['expires_at'] ?? '')) > self::TOKEN_REFRESH_MARGIN
        ) {
            return $cached;
        }

        try {
            $issued = $this->api->assistantToken($key);
        } catch (SmartyParcelErrorException $e) {
            // The key is revoked or unknown: drop it, the consent screen is shown again on the next open
            if ($e->getCode() === self::ERROR_UNAUTHORIZED) {
                delete_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_KEY);
                delete_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_TOKEN);
            }

            throw $e;
        }

        if (empty($issued['token']) || empty($issued['expires_at'])) {
            throw new \RuntimeException('Malformed assistant token response');
        }

        $token = [
            'token' => (string)$issued['token'],
            'expires_at' => (string)$issued['expires_at'],
        ];
        update_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_TOKEN, $token, false);

        return $token;
    }

    /**
     * What the assistant is told about the store when a chat starts (the API takes at most 8000 bytes as JSON).
     */
    public function getContext(): array
    {
        $meta = get_file_data(WC_UKR_SHIPPING_PLUGIN_ENTRY, ['Version' => 'Version']);

        $context = [
            'app_version' => $meta['Version'] ?? '',
            'php_version' => PHP_VERSION,
            'wp_version' => get_bloginfo('version'),
            'smartyparcel_status' => SmartyParcelHelper::isConnected()
                ? 'connected'
                : (SmartyParcelHelper::isGuest() ? 'guest' : 'not_connected'),
            'active_carriers' => array_values($this->carrierService->getActiveSlugs()),
        ];

        if (wcus_is_woocommerce_active()) {
            $context['wc_version'] = WC()->version;
            $context['country'] = WC()->countries->get_base_country();
            $context['currency'] = get_woocommerce_currency();
        }

        return $context;
    }

    private function getKey(): string
    {
        return (string)get_option(WCUS_OPTION_SMARTY_PARCEL_ASSISTANT_KEY, '');
    }

    private function expiresIn(string $expiresAt): int
    {
        $timestamp = strtotime($expiresAt);

        return $timestamp === false ? 0 : $timestamp - time();
    }
}
