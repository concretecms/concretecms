<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Routing\RouteCollection;
use Concrete\Core\Filesystem\FileLocator;

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
    protected $prefix = '';

    /**
     * The ability to set a common namespace for all classes within a group
     */
    protected $namespace = '';

    /**
     * Define one or more scope (comma-delimited) that apply to this route. Used with API routes.
     */
    protected $scope = '';

    /**
     * @var RouteMiddleware[]
     */
    protected $middlewares = [];

    /**
     * Regular expressions that lock down URL parameters to certain conditions.
     */
    protected $requirements = [];

    /**
     * RouteGroupBuilder constructor.
     * @param Router $router
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
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix .= '/' . trim($prefix, '/');
        return $this;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function scope($scope)
    {
        if ($this->scope == '') {
            $this->scope = $scope;
        } else {
            $this->scope .= ',' . $scope;
        }
        return $this;
    }

    public function buildGroup()
    {
        $group = new RouteGroupBuilder($this->router);
        $group->scope($this->scope);
        foreach($this->middlewares as $middleware) {
            $group->addMiddleware($middleware);
        }
        $group->setPrefix($this->prefix);
        $group->setNamespace($this->namespace);
        $group->setRequirements($this->requirements);
        return $group;
    }


    /**
     * @param string|object $middleware
     * @param int $priority
     * @return $this
     */
    public function addMiddleware($middleware, $priority = 10)
    {
        if (!($middleware instanceof RouteMiddleware)) {
            $routeMiddleware = new RouteMiddleware();
            $routeMiddleware->setMiddleware($middleware);
            $routeMiddleware->setPriority($priority);
        } else {
            $routeMiddleware = $middleware;
        }
        $this->middlewares[] = $routeMiddleware;
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
     * @return $this
     */
    public function setNamespace($namespace)
    {
        // first, normalize the namespace
        $namespace = trim($namespace, '\\');
        $this->namespace .= $namespace;
        return $this;
    }

    /**
     * @param array $requirements
     * @return $this
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
        return $this;
    }

    protected function processPrefix(Route $route)
    {
        if ($this->prefix) {
            $path = '/' . trim($this->prefix, '/') . $route->getPath();
            $route->setPath($path);
        }
    }

    protected function processRequirements(Route $route)
    {
        if ($this->requirements) {
            $route->setRequirements($this->requirements);
        }
    }


    protected function processNamespace(Route $route)
    {
        if ($this->namespace) {
            if (is_string($route->getAction()) && !(strpos($route->getAction(), '\\') === 0)) {
                $controller = [$this->namespace, trim($route->getAction(), '\\')];
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

    protected function processScope(Route $route)
    {
        if (!$route->getOption('oauth_scopes')) {
            $route->setOption('oauth_scopes', $this->scope);
        }
    }


    protected function sendFromGroupToRouter(RouteCollection $routeCollection, Router $router)
    {
        foreach($routeCollection->getIterator() as $name => $route) {
            $this->processRequirements($route);
            $this->processPrefix($route);
            $this->processMiddlewares($route);
            $this->processNamespace($route);
            $this->processScope($route);
            $router->addRoute($route);
        }
    }

    /**
     * @param $routes
     * @param null $pkgHandle
     * @return $this
     */
    public function routes($routes, $pkgHandle = null)
    {
        // First, create a new, empty router for use with the routes passed in the callable.
        $router = new Router(new RouteCollection(), $this->router->getActionFactory());
        if (is_callable($routes)) {
            // Run the callable with our empty router.
            $routes($router);
            // Grab the routes from the router, and pass them to our route group builder.
        } else if (is_string($routes)) {
            $app = Facade::getFacadeApplication();
            /**
             * @var $locator FileLocator
             */
            $locator = $app->make(FileLocator::class);
            if ($pkgHandle) {
                $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
            }
            $file = $locator->getRecord(DIRNAME_ROUTES . DIRECTORY_SEPARATOR . $routes);
            if ($file->exists()) {
                require $file->getFile();
            }
        } else {
            throw new \RuntimeException(t('Invalid input passed to RouteGroupBuilder::routes'));
        }
        $this->sendFromGroupToRouter($router->getRoutes(), $this->router);
        return $this;
    }


}
