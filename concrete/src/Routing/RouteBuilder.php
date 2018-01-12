<?php
namespace Concrete\Core\Routing;

class RouteBuilder
{

    /**
     * @var $router Router
     */
    protected $router;

    /**
     * @var $route Route
     */
    protected $route;

    /**
     * RouteBuilder constructor.
     * @param Router $router
     * @param Route $route
     */
    public function __construct(Router $router, Route $route)
    {
        $this->route = $route;
        $this->router = $router;
        $this->router->addRoute($route);
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->route->updateName($name, $this->router);
        return $this;
    }

    /**
     * @param string $middlewareClassName
     */
    public function addMiddleware($middlewareClassName, $priority = 10)
    {
        $middleware = new RouteMiddleware();
        $middleware->setPriority($priority);
        $middleware->setMiddleware($middlewareClassName);
        $this->route->addMiddleware($middleware);
        return $this;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function __call($method, $arguments)
    {
        $r = call_user_func_array(array($this->route, $method), $arguments);
        if ($r !== null) {
            return $r; // handle the get* methods
        }
        return $this; // set methods return the builder so it can chain.
    }





}
