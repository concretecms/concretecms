<?php

namespace Concrete\Core\Site;

use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton('Concrete\Core\Site\Service');
        $this->app->alias('Concrete\Core\Site\Service', 'site');

        $this->app->singleton('Concrete\Core\Site\Type\Service');
        $this->app->alias('Concrete\Core\Site\Type\Service', 'site/type');

        $this->app->singleton('Concrete\Core\Site\Resolver\DriverInterface', function ($app) {
            return $app->make('Concrete\Core\Site\Resolver\StandardDriver');
        });
    }
}
