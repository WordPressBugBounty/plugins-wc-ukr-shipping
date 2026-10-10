<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Http\Middleware;

use kirillbdev\WCUkrShipping\Helpers\WCUSHelper;
use kirillbdev\WCUSCore\Http\Request;

if ( ! defined('ABSPATH')) {
    exit;
}

final class CheckManageWooPermission
{
    public function handle(Request $request): void
    {
        if ( ! WCUSHelper::currentUserCanManageStore()) {
            wp_send_json([
                'success' => false,
                'error' => [
                    'message' => __('You do not have permission to access this action.', 'wc-ukr-shipping')
                ]
            ], 403);
        }
    }
}
