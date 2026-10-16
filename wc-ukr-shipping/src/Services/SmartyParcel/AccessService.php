<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Services\SmartyParcel;

use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Exceptions\SmartyParcel\AccessKeyRequiredException;

if ( ! defined('ABSPATH')) {
    exit;
}

class AccessService
{
    public const ACCESS_KEY_MAX_LENGTH = 100;

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
        if ($accessKey === '') {
            throw new AccessKeyRequiredException(
                __('An access key is now required to work with SmartyParcel. If you are the store owner, simply reconnect the store to SmartyParcel. Otherwise, ask the store owner for an access key and enter it in your user profile (Users → Profile → SmartyParcel).', 'wc-ukr-shipping')
            );
        }

        // Cache is dropped by saveAccessKey() on every key change, so it always belongs to the current key
        $cached = get_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_JWT, true);
        if (is_array($cached) && (int)($cached['expires_at'] ?? 0) > time()) {
            return $cached;
        }

        $token = $this->api->sendRequest('/v1/embedded/authx', [
            'access_key' => $accessKey,
        ]);
        update_user_meta($userId, WCUS_USER_META_SMARTY_PARCEL_JWT, $token);

        return $token;
    }
}
