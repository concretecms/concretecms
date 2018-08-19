<?php

namespace Concrete\Core\Routing;

abstract class AbstractRouteProvider implements RouteProviderInterface
{

    use RouterTrait;

    /** @var \Concrete\Core\Routing\RouteCollector */
    protected $routeCollector;

    /** @var \Concrete\Core\Routing\Router */
    protected $router;

    /**
     * Provide routes to a router
     *
     * @param \Concrete\Core\Routing\RouterInterface $router
     * @param \Concrete\Core\Routing\RouteCollector|null $collector
     * @return void
     */
    public function registerRoutes(RouterInterface $router, RouteCollector $collector = null)
    {
        $this->router = $router;
        $this->routeCollector = $collector;

        $this->register();
    }

    /**
     * Register routes
     * @return void
     */
    abstract public function register();

    /**
     * Add a simple route
     *
     * @param string|string[] $methods GET | POST | PUT | PATCH | HEAD | DELETE | OPTIONS
     * @param $path The path to the route `/path/to/route`
     * @param string|callable|array $handler
     * @param callable $factory A callable to help build the route `function(Route $route): Route`
     * @return \Concrete\Core\Routing\Route
     */
    public function to($methods, $path, $handler)
    {
        if (!$this->router) {
            throw new \RuntimeException('A router has not been set.');
        }

        return $this->router->to($methods, $path, $handler);
    }

    /**
     * @param $prefix
     * @param $handler
     * @param string $pkgHandle
     * @return static
     */
    public function group($prefix, $handler, $pkgHandle = null)
    {
        $this->router->group($prefix, $handler, $pkgHandle);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->routeCollector->getPrefix();
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->routeCollector->setPrefix($prefix);
        return $this;
    }

    /**
     * @param string|\Concrete\Core\Http\Middleware\MiddlewareInterface $middleware
     * @return $this
     */
    public function addMiddleware($middleware, $priority = 10)
    {
        $this->routeCollector->addMiddleware($middleware, $priority);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getScopes()
    {
        return $this->routeCollector->getScopes();
    }

    /**
     * @param string[] $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->routeCollector->setScopes($scopes);
        return $this;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function addScope($scope)
    {
        $this->routeCollector->addScope($scope);
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->routeCollector->getNamespace();
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->routeCollector->setNamespace($namespace);
        return $this;
    }

    /**
     * @param array $requirements
     * @return $this
     */
    public function setRequirements(array $requirements)
    {
        $this->routeCollector->setRequirements($requirements);
        return $this;
    }
}
