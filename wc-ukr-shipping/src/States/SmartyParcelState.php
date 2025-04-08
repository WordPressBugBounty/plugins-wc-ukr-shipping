<?php

namespace kirillbdev\WCUkrShipping\States;

use kirillbdev\WCUkrShipping\Includes\AppState;
use kirillbdev\WCUkrShipping\Services\SmartyParcelService;

if ( ! defined('ABSPATH')) {
    exit;
}

class SmartyParcelState extends AppState
{
    private SmartyParcelService $smartyParcelService;

    public function __construct(SmartyParcelService $smartyParcelService)
    {
        $this->smartyParcelService = $smartyParcelService;
    }

    protected function getState(): array
    {
        $authStatus = $this->getAuthState();

        return [
            'auth_state' => $authStatus,
            'account' => $authStatus === 'connected'
                ? $this->smartyParcelService->getAccountInfo()
                : [],
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
