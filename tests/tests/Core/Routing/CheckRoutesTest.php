<?php
namespace Concrete\Tests\Core\Routin;

use PHPUnit_Framework_TestCase;
use Concrete\Core\Support\Facade\Application;

class CheckRoutesTest extends PHPUnit_Framework_TestCase
{
    public function routeDestinationProvider()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $routes = $config->get('app.routes');
        $result = [];
        foreach ($routes as $path => $data) {
            if (is_array($data) && isset($data[0]) && is_string($data[0]) && $data[0] !== '') {
                $result[] = [$path, $data[0]];
            }
        }

        return $result;
    }

    /**
     * @dataProvider routeDestinationProvider
     */
    public function testRouteDestination($path, $callable)
    {
        if (preg_match('/^(.+)::(__construct)$/', $callable, $m)) {
            $this->assertTrue(class_exists($m[1], true), "Invalid route for path $path: $callable");
        } else {
            $this->assertTrue(is_callable($callable), "Invalid route for path $path: $callable");
        }
    }
}
