<?php
namespace Concrete\Core\Database;

use Concrete\Core\Database\Connection\ConnectionFactory;
use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'database',
            function ($cms) {
                $driver_manager = new DriverManager($cms);
                $driver_manager->configExtensions(\Config::get('database.drivers'));
                $factory = new ConnectionFactory($cms, $driver_manager);
                return new DatabaseManager($cms, $factory);
            });
    }

}
