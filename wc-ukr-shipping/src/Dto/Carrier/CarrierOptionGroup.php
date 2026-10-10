<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Dto\Carrier;

if ( ! defined('ABSPATH')) {
    exit;
}

final class CarrierOptionGroup
{
    public string $title;

    /**
     * @var CarrierOptionField[]
     */
    public array $fields;

    /**
     * @param CarrierOptionField[] $fields
     */
    public function __construct(string $title, array $fields = [])
    {
        $this->title = $title;
        $this->fields = $fields;
    }

    public function isEmpty(): bool
    {
        return $this->fields === [];
    }
}
