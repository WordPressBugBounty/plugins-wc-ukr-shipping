<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Component\Cache;

use kirillbdev\WCUkrShipping\Contracts\Cache\LockProviderInterface;

class TransientLockProvider implements LockProviderInterface
{
    public function lock(string $eky, int $seconds): bool
    {
        $lock = get_transient($this->getKey($eky));
        if ($lock !== false) {
            return false;
        }

        set_transient($this->getKey($eky), 1, $seconds);

        return true;
    }

    public function releaseLock(string $eky): bool
    {
        delete_transient($this->getKey($eky));

        return true;
    }

    private function getKey(string $eky): string
    {
        return 'wcus_lock:' . md5($eky);
    }
}
