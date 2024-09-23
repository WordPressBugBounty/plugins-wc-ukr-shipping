<?php

namespace kirillbdev\WCUkrShipping\Modules\Core;

use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\DB\Migrator;
use kirillbdev\WCUSCore\Exceptions\MigrateException;

class Activator implements ModuleInterface
{
    private Migrator $migrator;

    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    public function init(): void
    {
        add_action('plugins_loaded', [$this, 'activate']);
        register_activation_hook(WC_UKR_SHIPPING_PLUGIN_ENTRY, [$this, 'activate']);
    }

    public function activate(): void
    {
        try {
            // All base tables (both in lite and pro versions) are added in core package
            $this->migrator->run();
        } catch (MigrateException $e) {
            // do nothing yet
        }
    }
}
