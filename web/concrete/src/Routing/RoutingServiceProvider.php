<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Foundation\Service\Provider;

class RoutingServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Concrete\Core\Routing\Router');
        $this->app->bind('Concrete\Core\Routing\RouterInterface', 'Concrete\Core\Routing\Router');
    }

}
