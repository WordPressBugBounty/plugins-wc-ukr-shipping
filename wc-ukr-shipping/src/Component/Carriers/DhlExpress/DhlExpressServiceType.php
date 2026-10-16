<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Carriers\DhlExpress;

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * DHL Express service types supported by SmartyParcel Rates API. Codes must match the platform ones.
 */
final class DhlExpressServiceType
{
    public const WORLDWIDE = 'dhl_express_worldwide';
    public const EXPRESS_0900 = 'dhl_express_0900';
    public const EXPRESS_1200 = 'dhl_express_1200';
    public const ECONOMY_SELECT = 'dhl_express_economy_select';
    public const DOMESTIC = 'dhl_express_domestic';

    /**
     * @return array<string, string> Default checkout names indexed by service type.
     */
    public static function all(): array
    {
        return [
            self::WORLDWIDE => 'DHL Express Worldwide',
            self::EXPRESS_0900 => 'DHL Express 9:00',
            self::EXPRESS_1200 => 'DHL Express 12:00',
            self::ECONOMY_SELECT => 'DHL Economy Select',
            self::DOMESTIC => 'DHL Express Domestic',
        ];
    }
}
