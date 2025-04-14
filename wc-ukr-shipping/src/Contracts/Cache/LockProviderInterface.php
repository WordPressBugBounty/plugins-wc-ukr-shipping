<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Contracts\Cache;

interface LockProviderInterface
{
    public function lock(string $eky, int $seconds): bool;

    public function releaseLock(string $eky): bool;
}
