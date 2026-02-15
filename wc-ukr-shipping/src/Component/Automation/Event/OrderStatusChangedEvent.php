<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Automation\Event;

use kirillbdev\WCUkrShipping\Component\Automation\Context;

if ( ! defined('ABSPATH')) {
    exit;
}

class OrderStatusChangedEvent implements EventInterface
{
    private string $newStatus;

    public function __construct(string $newStatus)
    {
        $this->newStatus = $newStatus;
    }

    public function canProcess(Context $context): bool
    {
        if ( strpos($this->newStatus, 'wc-' ) === 0 ) {
            $this->newStatus = substr($this->newStatus, 3);
        }

        return $context->getOrder()->has_status($this->newStatus);
    }
}
