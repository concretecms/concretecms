<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Routing\RouteCollection;
use Concrete\Core\Controller\Controller;

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
