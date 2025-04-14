<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Automation\Action;

use kirillbdev\WCUkrShipping\Component\Automation\Context;

if ( ! defined('ABSPATH')) {
    exit;
}

class AddOrderNoteAction implements ActionInterface
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function execute(Context $context): void
    {
        $order = wcus_wrap_order($context->getOrder());
        $compiled = str_replace(
            [
                '{{tracking_number}}',
                '{{carrier_status}}',
                '{{order_id}}',
                '{{billing_firstname}}',
                '{{billing_lastname}}',
                '{{city}}',
                '{{address}}',
            ],
            [
                $context->getLabel()['tracking_number'],
                $context->getLabel()['carrier_status'],
                $order->getOrigin()->get_id(),
                $order->getOrigin()->get_billing_first_name(),
                $order->getOrigin()->get_billing_last_name(),
                $order->getCity(),
                $order->getAddress1(),
            ],
            $this->message
        );
        $order->getOrigin()->add_order_note($compiled);
    }
}
