<?php
namespace Concrete\Core\API;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class APIServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(APIFactory::class, function($app) {
            $config = $app->make('config');
            return new APIFactory($config);
        });

        $this->app->singleton('api', function ($app) {
            return $app->make(APIFactory::class);
        });
    }

}