<?php
namespace Concrete\Core\Site;

use Concrete\Core\Foundation\Service\Provider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton('site', function() use ($app) {
            return $app->make('Concrete\Core\Site\Service');
        });
    }
}
