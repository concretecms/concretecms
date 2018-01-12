<?php
namespace Concrete\Core\Routing;

use Symfony\Component\Routing\RouteCollection;

class RouteActionFactory implements RouteActionFactoryInterface
{

    public function createAction(Route $route)
    {
        if ($route->getAction() instanceof \Closure) {
            return new ClosureRouteAction($route->getAction());
        }
        if (is_string($route->getAction())) {
            return new ControllerRouteAction($route->getAction());

        }
    }

}
