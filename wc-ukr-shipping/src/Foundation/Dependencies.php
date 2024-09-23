<?php

namespace kirillbdev\WCUkrShipping\Foundation;

use kirillbdev\WCUkrShipping\Api\CloudApi;
use kirillbdev\WCUkrShipping\Contracts\Customer\CustomerStorageInterface;
use kirillbdev\WCUkrShipping\Contracts\NovaPoshtaAddressProviderInterface;
use kirillbdev\WCUkrShipping\DB\Repositories\AreaRepositoryInterface;
use kirillbdev\WCUkrShipping\DB\Repositories\HardcodedAreaRepository;
use kirillbdev\WCUkrShipping\Http\Controllers\AddressController;
use kirillbdev\WCUkrShipping\Includes\Customer\LoggedCustomerStorage;
use kirillbdev\WCUkrShipping\Includes\Customer\SessionCustomerStorage;
use kirillbdev\WCUkrShipping\Modules\Core\Activator;
use kirillbdev\WCUSCore\DB\Migrator;
use kirillbdev\WCUkrShipping\Address\Provider\MySqlAddressProvider;

if ( ! defined('ABSPATH')) {
    exit;
}

final class Dependencies
{
    public static function all()
    {
        return [
            // Contracts
            CustomerStorageInterface::class => function ($container) {
                $customerId = wc()->customer->get_id();

                return $container->make($customerId ? LoggedCustomerStorage::class : SessionCustomerStorage::class);
            },
            NovaPoshtaAddressProviderInterface::class => function ($container) {
                return $container->make(CloudApi::class);
            },
            AddressProviderInterface::class => function ($container) {
                return $container->make(MySqlAddressProvider::class);
            },
            AreaRepositoryInterface::class => function ($container) {
                return $container->make(HardcodedAreaRepository::class);
            },
            // Modules
            Activator::class => function ($container) {
                return new Activator($container->make(Migrator::class));
            },
            // Controllers
            AddressController::class => function ($container) {
                return new AddressController($container->make(MySqlAddressProvider::class));
            }
        ];
    }
}
