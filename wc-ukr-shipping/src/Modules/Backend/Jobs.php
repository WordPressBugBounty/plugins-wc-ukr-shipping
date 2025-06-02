<?php

declare(strict_types=1);

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Component\Task\Task;
use kirillbdev\WCUkrShipping\Services\TaskService;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

if ( ! defined('ABSPATH')) {
    exit;
}

class Jobs implements ModuleInterface
{
    private TaskService $taskService;

    private array $workers = [
        '1.0.1' => 'workersV100'
    ];

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function init(): void
    {
        add_filter('cron_schedules', [$this, 'registerCustomIntervals']);
        add_action('plugins_loaded', [$this, 'initWorkers']);
    }

    public function registerCustomIntervals(array $schedules): array
    {
        $schedules['wcus_every_minute'] = [
            'interval'  => 60,
            'display'   => __('Every minute', 'wc-ukr-shipping-i18n'),
        ];

        return $schedules;
    }

    public function initWorkers(): void
    {
        $currentVersion = get_option('wc_ukr_shipping_workers_version', '');

        foreach ($this->workers as $version => $callback) {
            if (version_compare($currentVersion, $version, '<' )) {
                try {
                    call_user_func([$this, $callback]);
                    $currentVersion = $version;
                } catch (\Throwable $e) {
                    break;
                }
            }
        }

        update_option('wc_ukr_shipping_workers_version', $currentVersion);
    }

    private function workersV100(): void
    {
        wp_unschedule_hook('wcus_tracking_worker_tick');
        $this->taskService->scheduleRepeatable(
            new Task('wcus_tracking_worker_tick'),
            time(),
            'wcus_every_minute'
        );
    }
}
