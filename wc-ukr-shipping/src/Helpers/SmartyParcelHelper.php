<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Helpers;

final class SmartyParcelHelper
{
    public static function isConnected(): bool
    {
        return get_option(WCUS_OPTION_SMARTY_PARCEL_USER_STATUS) === 'connected';
    }
}
