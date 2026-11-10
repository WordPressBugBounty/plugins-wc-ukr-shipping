<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules;

use kirillbdev\WCUkrShipping\Api\SmartyParcelWPApi;
use kirillbdev\WCUkrShipping\Services\SmartyParcel\AccessService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class SmartyParcel implements ModuleInterface
{
    private SmartyParcelWPApi $api;
    private AccessService $accessService;

    public function __construct(SmartyParcelWPApi $api, AccessService $accessService)
    {
        $this->api = $api;
        $this->accessService = $accessService;
    }

    public function init()
    {
        add_action('init', [$this, 'handleOauthCallback']);
    }

    public function handleOauthCallback(): void
    {
        if (!isset($_GET['page']) || $_GET['page'] !== 'wcus_oauth_callback') {
            return;
        }

        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        if (!isset($_GET['access_token']) || !wp_verify_nonce($_GET['nonce'] ?? '', 'smartyparcel_oauth')) {
            return;
        }

        try {
            if (get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) !== 'connected') {
                $response = $this->api->connectApplication(sanitize_text_field($_GET['access_token']));

                update_option(WCUS_OPTION_SMARTY_PARCEL_API_KEY, $response['api_key']);
                update_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS, 'connected');

                // Owner access key is issued on every (re)connect and belongs to the user who connected the store
                if (!empty($response['access_key'])) {
                    $this->accessService->saveAccessKey(get_current_user_id(), $response['access_key']);
                }

                // The guest key is already overwritten by the store key above and revoked by the platform
                delete_option(WCUS_OPTION_SMARTY_PARCEL_GUEST_ID);
            }

            $dashboardUrl = admin_url('admin.php?page=wcus_smarty_parcel');
            $returnTo = isset($_GET['return_to']) ? esc_url_raw(wp_unslash($_GET['return_to'])) : '';
            // Only back into this admin: an external return_to falls back to the dashboard
            if ($returnTo === '' || strpos($returnTo, admin_url()) !== 0) {
                $returnTo = $dashboardUrl;
            }

            wp_safe_redirect($returnTo);
            exit;
        } catch (\Exception $e) {
            echo 'Oauth failed. Please try again or contact the support team.';
            exit;
        }
    }
}
