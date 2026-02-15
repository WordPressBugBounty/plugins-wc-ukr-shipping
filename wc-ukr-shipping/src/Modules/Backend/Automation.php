<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Component\Automation\Context;
use kirillbdev\WCUkrShipping\Services\AutomationService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

class Automation implements ModuleInterface
{
    private AutomationService $automationService;

    public function __construct(AutomationService $automationService)
    {
        $this->automationService = $automationService;
    }

    public function init(): void
    {
        add_action( 'woocommerce_order_status_changed', [$this, 'fireUpdateOrderAutomation'], 10, 3);
    }

    public function fireUpdateOrderAutomation(int $orderId, string $fromStatus, string $toStatus): void
    {
        $order = wc_get_order($orderId);
        if (!$order || $order->get_type() !== 'shop_order') {
            return;
        }

        $this->automationService->executeEvent(
                AutomationService::EVENT_ORDER_STATUS_CHANGED,
            new Context(
                AutomationService::EVENT_ORDER_STATUS_CHANGED,
                wc_get_order($orderId),
                []
            )
        );
    }
}
