<?php

namespace Concrete\Core\Command\Task;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Command\Process\Logger\StandardLoggerFactory;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
        $this->app->singleton(
            LoggerFactoryInterface::class,
            function (Application $app) {
                $factory = $app->make(StandardLoggerFactory::class);
                return $factory;
            }
        );

    }
}
