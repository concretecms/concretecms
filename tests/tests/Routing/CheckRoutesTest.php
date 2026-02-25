<?php

namespace Concrete\Tests\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Routing\MatchedRoute;
use Concrete\Core\Routing\Router;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Tests\TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;

class CheckRoutesTest extends TestCase
{
    public function routeDestinationProvider()
    {
        $app = ApplicationFacade::getFacadeApplication();
        /** @var \Concrete\Core\Routing\Router $router */
        $router = $app->make('router');
        $routes = $router->getRoutes();
        $result = [];
        /**
         * @var \Concrete\Core\Routing\Route $route
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
                $this->assertTrue(class_exists($m[1], true), "No class! Invalid route for path {$path} : {$callable}");
                $checked = true;
            } elseif (interface_exists($class, true)) {
                $this->assertTrue(method_exists($class, $method), "No Method! Invalid route for path {$path} : {$callable}");
                $checked = true;
            } elseif ($app->isAlias($class)) {
                $this->assertTrue(method_exists($class, $method), "Alias but no method! Invalid route for path {$path} : {$callable}");
            }
        }
        if ($checked === false) {
            if (PHP_MAJOR_VERSION < 8) {
                $this->assertTrue(is_callable($callable), "Not callable! Invalid route for path {$path} : {$callable}");
            } else {
                // PHP 8 is_callable only works on static methods
                // get_class_methods only returns public methods so its similar to the old behaviour
                $this->assertTrue(class_exists($class) && in_array($method, get_class_methods($class)), "Not callable! Invalid route for path {$path} : {$callable}");
            }

        }
    }

    public function provideRouteWithDefaultParameters(): array
    {
        $app = ApplicationFacade::getFacadeApplication();
        $result = [];
        for ($flags = 0b0000; $flags <= 0b1111; $flags++) {
            $result[] = [
                $app,
                ($flags & 0b0001) !== 0,
                ($flags & 0b0010) !== 0,
                ($flags & 0b0100) !== 0,
                ($flags & 0b1000) !== 0,
            ];
        }

        return $result;
    }

    /**
     * @dataProvider provideRouteWithDefaultParameters
     */
    public function testRouteWithArguments(Application $app, bool $requestArgumentValue, bool $requestWithTrailingSlash, bool $routeWithTrailingShash, bool $routeWithDefaultArgumentValue): void
    {
        $router = $app->build(Router::class);
        $request = Request::create('http://localhost/foo/bar' . ($requestArgumentValue ? '/custom-baz' : '') . ($requestWithTrailingSlash ? '/' : ''));
        $route = $router->register('/foo/bar/{baz}' . ($routeWithTrailingShash ? '/' : ''), static function ($baz) { return $baz; });
        if ($routeWithDefaultArgumentValue) {
            $route->setDefaults(['baz' => 'default-baz']);
        }
        $matchedRoute = null;
        $marchError = null;
        try {
            $matchedRoute = $router->matchRoute($request);
        } catch (Throwable $x) {
            $marchError = $x;
        }
        if ($requestArgumentValue === false && $routeWithDefaultArgumentValue === false) {
            $this->assertInstanceOf(ResourceNotFoundException::class, $marchError);
        } else {
            $this->assertInstanceOf(MatchedRoute::class, $matchedRoute);
            $this->assertSame($route, $matchedRoute->getRoute());
            $foundArguments = $matchedRoute->getArguments();
            $this->assertArrayHasKey('baz', $foundArguments);
            if ($requestArgumentValue) {
                $this->assertSame('custom-baz', $foundArguments['baz']);
            } else {
                $this->assertSame('default-baz', $foundArguments['baz']);
            }
        }
    }
}
