<?php

namespace Concrete\Core\Routing;

trait RouterTrait
{

    /**
     * Add a simple route
     *
     * @param string|string[] $methods GET | POST | PUT | PATCH | HEAD | DELETE | OPTIONS
     * @param $path The path to the route `/path/to/route`
     * @param string|callable|array $handler
     * @param callable $factory A callable to help build the route `function(Route $route): Route`
     * @return \Concrete\Core\Routing\Route
     */
    abstract public function to($methods, $path, $handler);

    /**
     * Add a route that matches the GET request method
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function get($path, $handler)
    {
        return $this->to(['GET', 'HEAD'], $path, $handler);
    }

    /**
     * Add a route that matches the POST request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function post($path, $handler)
    {
        return $this->to('POST', $path, $handler);
    }

    /**
     * Add a route that matches the PUT request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function put($path, $handler)
    {
        return $this->to('PUT', $path, $handler);
    }

    /**
     * Add a route that matches the PATCH request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function patch($path, $handler)
    {
        return $this->to('PATCH', $path, $handler);
    }

    /**
     * Add a route that matches the HEAD request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function head($path, $handler)
    {
        return $this->to('HEAD', $path, $handler);
    }

    /**
     * Add a route that matches the DELETE request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function delete($path, $handler)
    {
        return $this->to('DELETE', $path, $handler);
    }

    /**
     * Add a route that matches the OPTIONS request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function options($path, $handler)
    {
        return $this->to('OPTIONS', $path, $handler);
    }

    /**
     * Add a route that matches any incoming request method
     *
     * @param string $path
     * @param string|callable|string[] $handler
     * @return \Concrete\Core\Routing\Route
     */
    public function any($path, $handler)
    {
        return $this->to(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], $path, $handler);
    }
}
