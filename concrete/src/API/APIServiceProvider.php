<?php
namespace Concrete\Core\API;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class APIServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('api', function ($app) {
            return $app->make('Concrete\Core\API\APIFactory');
        });
    }

}