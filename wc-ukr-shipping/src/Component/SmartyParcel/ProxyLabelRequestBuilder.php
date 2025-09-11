<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\SmartyParcel;

class ProxyLabelRequestBuilder implements LabelRequestBuilderInterface
{
    private array $labelRequest;

    public function __construct(array $labelRequest)
    {
        $this->labelRequest = $labelRequest;
    }

    public function build(): array
    {
        return $this->labelRequest;
    }
}
