<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Services\SmartyParcel;

use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\AccessKeyRequiredException;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\SmartyParcelErrorException;

if ( ! defined('ABSPATH')) {
    exit;
}

class AccessService
{
    public const ACCESS_KEY_MAX_LENGTH = 100;

    private const ERROR_ACCESS_KEY_INVALID = 1509;

    private SmartyParcelWPApi $api;

    public function __construct(SmartyParcelWPApi $api)
    {
        $this->api = $api;
    }

    public function getAccessKey(int $userId): string
    {
        return (string)get_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_ACCESS_KEY, true);
    }

    public function saveAccessKey(int $userId, string $accessKey): void
    {
        $accessKey = trim($accessKey);
        if (mb_strlen($accessKey) > self::ACCESS_KEY_MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf(
                    /* translators: %d: maximum length of the access key */
                    __('Access key must not be longer than %d characters.', 'wc-ukr-shipping'),
                    self::ACCESS_KEY_MAX_LENGTH
                )
            );
        }

        if ($accessKey === $this->getAccessKey($userId)) {
            return;
        }

        if ($accessKey === '') {
            delete_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_ACCESS_KEY);
        } else {
            update_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_ACCESS_KEY, $accessKey);
        }

        // A token issued for the previous key must not outlive it
        delete_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_JWT);
    }

    /**
     * @throws AccessKeyRequiredException
     */
    public function issueToken(int $userId): array
    {
        $accessKey = $this->getAccessKey($userId);

        // Cache is dropped by saveAccessKey() on every key change, so it always belongs to the current key
        $cached = get_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_JWT, true);
        if (is_array($cached) && (int)($cached['expires_at'] ?? 0) > time()) {
            return $cached;
        }

        try {
            $token = $this->api->sendRequest('/v1/embedded/authx', [
                'access_key' => $accessKey !== '' ? $accessKey : null,
            ]);
        } catch (SmartyParcelErrorException $e) {
            if ($e->getCode() !== self::ERROR_ACCESS_KEY_INVALID) {
                throw $e;
            }

            throw new AccessKeyRequiredException(
                $accessKey === ''
                    ? __('Access keys are enabled for this SmartyParcel account, so an access key is required. If you are the store owner, simply reconnect the store to SmartyParcel. Otherwise, ask the store owner for an access key and enter it in your user profile (Users → Profile → SmartyParcel).', 'wc-ukr-shipping')
                    : __('Your SmartyParcel access key is invalid or has been revoked. If you are the store owner, simply reconnect the store to SmartyParcel. Otherwise, ask the store owner for a new access key and enter it in your user profile (Users → Profile → SmartyParcel).', 'wc-ukr-shipping')
            );
        }
        update_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_JWT, $token);

        return $token;
    }
}
