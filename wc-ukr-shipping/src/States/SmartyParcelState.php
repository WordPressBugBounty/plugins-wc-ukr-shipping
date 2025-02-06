<?php

namespace kirillbdev\WCUkrShipping\States;

use kirillbdev\WCUkrShipping\Includes\AppState;

if ( ! defined('ABSPATH')) {
    exit;
}

class SmartyParcelState extends AppState
{
    protected function getState(): array
    {
        return [
            'auth_state' => $this->getAuthState(),
        ];
    }

    private function getAuthState(): string
    {
        $userStatus = get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS);
        switch ($userStatus) {
            case 'waiting_verification':
                return 'waiting_verification';
            case 'connected':
                return 'connected';
            default:
                return 'login';
        }
    }
}
