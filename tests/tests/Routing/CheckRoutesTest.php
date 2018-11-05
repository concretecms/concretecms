<?php

namespace Concrete\Tests\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use PHPUnit_Framework_TestCase;

class CheckRoutesTest extends PHPUnit_Framework_TestCase
{
    public function routeDestinationProvider()
    {
        
        $app = ApplicationFacade::getFacadeApplication();
        /** @var $router  \Concrete\Core\Routing\Router */
        $router = $app->make('router');
        $routes = $router->getRoutes();
        $result = [];
        /**
         * @var  $route \Concrete\Core\Routing\Route
         */
        foreach ($routes as $route) {
            $data = $route->getAction();
            $path = $route->getPath();
            if (!empty($data) && is_string($data)) {
                $result[] = [$app, $path, $data];
            }
        }


        return $result;
    }

/** @dataProvider routeDestinationProvider
 *
 * @param $app Application
 * @param $path string
 * @param $callable mixed
 */
    public function testRouteDestination(Application $app, $path, $callable)
    {

        $checked = false;
        if (preg_match('/^([^:]+)::([^:]+)$/', $callable, $m)) {
            $class = $m[1];
            $method = $m[2];
            if ($method === '__construct') {
                $this->assertTrue(class_exists($m[1], true), "No class! Invalid route for path $path : $callable");
                $checked = true;
            } elseif (interface_exists($class, true)) {
                $this->assertTrue(method_exists($class, $method), "No Method! Invalid route for path $path : $callable");
                $checked = true;
            } elseif ($app->isAlias($class)) {
                $this->assertTrue(method_exists($class, $method), "Alias but no method! Invalid route for path $path : $callable");
            }
        }
        if ($checked === false) {
            $this->assertTrue(is_callable($callable), "Not callable! Invalid route for path $path : $callable");
        }
    }
}
