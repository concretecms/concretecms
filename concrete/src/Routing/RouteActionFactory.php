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
        $action = $route->getAction();

        // Allow passing through explicit route actions
        if ($action instanceof RouteActionInterface) {
            return $action;
        }

        if ($action instanceof \Closure) {
            return new ClosureRouteAction($route->getAction());
        }

        $class = null;
        $method = null;
        if (is_string($action)) {
            list($class, $method) = explode('::', $action . '::');
            $method = $method ?: '__invoke';
        } else {
            $class = $action[0];
            $method = $action[1];
        }

        $reflected = new \ReflectionClass($class);
        if ($reflected->isSubclassOf(AbstractController::class)) {
            return new ControllerRouteAction($action);
        }

        $app = Facade::getFacadeApplication();
        return new ApplicationRouteAction($app, [$class, $method]);
    }

}
