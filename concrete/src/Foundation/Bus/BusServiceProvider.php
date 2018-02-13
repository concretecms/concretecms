<?php
namespace Concrete\Core\Foundation\Bus;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class BusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Bus::class, function($app) {
            return new Bus($app, $app->make('config'));
        });
        $this->app->bind('command/bus', Bus::class);
    }
}
