<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class DispatcherServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DispatcherFactory::class, function($app) {
            return new DispatcherFactory($app, $app->make('config'));
        });
    }
}
