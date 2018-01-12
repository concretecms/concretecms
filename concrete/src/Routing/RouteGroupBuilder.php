<?php
namespace Concrete\Core\Routing;

use Symfony\Component\Routing\RouteCollection;

class RouteGroupBuilder
{

    /**
     * This is the original router, not the temporary router we created for the route group callable.
     * @var $router Router
     */
    protected $router;

    /**
     * A path prefix for all routes.
     * @var string
     */
    protected $prefix;

    /**
     * The ability to set a common namespace for all classes within a group
     */
    protected $namespace;

    /**
     * @var RouteMiddleware[]
     */
    protected $middlewares = [];

    /**
     * RouteBuilder constructor.
     * @param Router $router
     * @param Route $route
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param string $middlewareClassname
     * @return $this
     */
    public function addMiddleware($middlewareClassname, $priority = 10)
    {
        $middleware = new RouteMiddleware();
        $middleware->setMiddleware($middlewareClassname);
        $middleware->setPriority($priority);
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        // first, normalize the namespace
        $namespace = trim($namespace, '\\');
        $this->namespace = $namespace;
        return $this;
    }

    protected function processPrefix(Route $route)
    {
        if ($this->prefix) {
            $name = $route->getName();
            $path = '/' . trim($this->prefix, '/') . $route->getPath();
            $route->setPath($path);
        }
    }

    protected function processNamespace(Route $route)
    {
        if ($this->namespace) {
            $action = $this->router->getAction($route);
            if ($action instanceof ControllerRouteAction) {
                $controller = [$this->namespace, $route->getAction()];
                $route->setAction(implode('\\', $controller));
            }
        }
    }

    protected function processMiddlewares(Route $route)
    {
        foreach($this->middlewares as $middleware) {
            $route->addMiddleware($middleware);
        }
    }

    protected function sendFromGroupToRouter(RouteCollection $routeCollection, Router $router)
    {
        foreach($routeCollection->getIterator() as $name => $route) {
            $this->processPrefix($route);
            $this->processMiddlewares($route);
            $this->processNamespace($route);
            $router->addRoute($route);
        }
    }

    public function routes(callable $routes)
    {
        // First, create a new, empty router for use with the routes passed in the callable.
        $router = new Router(new RouteCollection(), $this->router->getActionFactory());
        // Run the callable with our empty router.
        $routes($router);
        // Grab the routes from the router, and pass them to our route group builder.
        $this->sendFromGroupToRouter($router->getRoutes(), $this->router);
    }


}
