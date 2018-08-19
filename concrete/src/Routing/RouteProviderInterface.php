<?php

namespace Concrete\Core\Routing;


interface RouteProviderInterface
{

    /**
     * Provide routes to a router
     *
     * @param \Concrete\Core\Routing\RouterInterface $router
     * @param \Concrete\Core\Routing\RouteCollector|null $collector
     * @return void
     */
    public function registerRoutes(RouterInterface $router, RouteCollector $collector = null);

}
