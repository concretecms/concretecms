<?php

namespace Concrete\Core\Routing;


interface RouteProviderInterface
{

    /**
     * Provide routes to a router
     *
     * @param \Concrete\Core\Routing\RouterInterface $router
     * @return void
     */
    public function registerRoutes(RouterInterface $router);

}
