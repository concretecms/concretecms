<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Theme\ThemeRouteCollection;

class RoutingServiceProvider extends Provider
{
    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->singleton(Router::class);
        $this->app->singleton(RouterInterface::class, Router::class);
        $this->app->bind(RouteActionFactoryInterface::class, function() {
            return new RouteActionFactory();
        });
        $this->app->bind('router', Router::class);
        $this->app->singleton(ThemeRouteCollection::class);
    }
}
