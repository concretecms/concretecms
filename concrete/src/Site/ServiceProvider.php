<?php
namespace Concrete\Core\Site;

use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;
use Concrete\Core\Site\Resolver\Resolver;
use Concrete\Core\Site\Resolver\StandardDriver;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton('site', function() use ($app) {
            return $app->make('Concrete\Core\Site\Service');
        });
        $this->app->singleton('site/type', function() use ($app) {
            return $app->make('Concrete\Core\Site\Type\Service');
        });

        $this->app->singleton('Concrete\Core\Site\Resolver\DriverInterface', function() use ($app) {
            $resolver = $this->app->make('Concrete\Core\Site\Resolver\StandardDriver');
            return $resolver;
        });
    }
}
