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
        $config = $app->make('config');
        $routes = $config->get('app.routes');
        $result = [];
        foreach ($routes as $path => $data) {
            if (is_array($data) && isset($data[0]) && is_string($data[0]) && $data[0] !== '') {
                $result[] = [$app, $path, $data[0]];
            }
        }

        return $result;
    }

    /**
     * @dataProvider routeDestinationProvider
     *
     * @param Application $app
     * @param mixed $path
     * @param mixed $callable
     */
    public function testRouteDestination(Application $app, $path, $callable)
    {
        $this->markTestSkipped('Skipping until we can rewrite for new router.');
        $checked = false;
        if (preg_match('/^([^:]+)::([^:]+)$/', $callable, $m)) {
            $class = $m[1];
            $method = $m[2];
            if ($method === '__construct') {
                $this->assertTrue(class_exists($m[1], true), "Invalid route for path $path: $callable");
                $checked = true;
            } elseif (interface_exists($class, true)) {
                $this->assertTrue(method_exists($class, $method), "Invalid route for path $path: $callable");
                $this->assertTrue($app->bound($class), "Invalid route for path $path: $callable");
                $checked = true;
            }
        }
        if ($checked === false) {
            $this->assertTrue(is_callable($callable), "Invalid route for path $path: $callable");
        }
    }
}
