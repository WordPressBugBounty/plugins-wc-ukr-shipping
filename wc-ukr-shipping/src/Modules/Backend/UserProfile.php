<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Services\SmartyParcel\AccessService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\Foundation\View;

if ( ! defined('ABSPATH')) {
    exit;
}

class UserProfile implements ModuleInterface
{
    private const FIELD_NAME = 'wcus_smartyparcel_access_key';

    private AccessService $accessService;

    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function init()
    {
        add_action('show_user_profile', [$this, 'renderFields']);
        add_action('edit_user_profile', [$this, 'renderFields']);
        add_action('personal_options_update', [$this, 'saveFields']);
        add_action('edit_user_profile_update', [$this, 'saveFields']);
        add_action('user_profile_update_errors', [$this, 'validateFields'], 10, 3);
    }

    public function renderFields(\WP_User $user): void
    {
        if ( ! $this->canManageStore($user)) {
            return;
        }

        echo View::render('user/access_key', [ // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template escapes its own output
            'fieldName' => self::FIELD_NAME,
            'accessKey' => $this->accessService->getAccessKey($user->ID),
            'maxLength' => AccessService::ACCESS_KEY_MAX_LENGTH,
        ]);
    }

    /**
     * Core verifies the "update-user_{$userId}" nonce before firing the update hooks.
     */
    public function saveFields(int $userId): void
    {
        $user = get_userdata($userId);
        if ( ! $user || ! current_user_can('edit_user', $userId) || ! $this->canManageStore($user)) {
            return;
        }

        $accessKey = $this->getSubmittedAccessKey();
        if ($accessKey === null) {
            return;
        }

        try {
            $this->accessService->saveAccessKey($userId, $accessKey);
        } catch (\InvalidArgumentException $e) {
            // Reported to the user by validateFields()
        }
    }

    public function validateFields(\WP_Error $errors, bool $update, \stdClass $user): void
    {
        $accessKey = $this->getSubmittedAccessKey();
        if ($accessKey !== null && mb_strlen($accessKey) > AccessService::ACCESS_KEY_MAX_LENGTH) {
            $errors->add(
                self::FIELD_NAME,
                sprintf(
                    /* translators: %d: maximum length of the access key */
                    __('SmartyParcel access key must not be longer than %d characters.', 'wc-ukr-shipping'),
                    AccessService::ACCESS_KEY_MAX_LENGTH
                )
            );
        }
    }

    private function getSubmittedAccessKey(): ?string
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is verified by WP core on profile update
        if ( ! isset($_POST[self::FIELD_NAME])) {
            return null;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is verified by WP core on profile update
        return trim(sanitize_text_field(wp_unslash((string)$_POST[self::FIELD_NAME])));
    }

    private function canManageStore(\WP_User $user): bool
    {
        return user_can($user, 'manage_woocommerce') || user_can($user, 'manage_options');
    }
}
