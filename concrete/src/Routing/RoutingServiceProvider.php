<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Foundation\Service\Provider;

class RoutingServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->singleton(Router::class);
        $this->app->bind(RouterInterface::class, Router::class);
    }
}
