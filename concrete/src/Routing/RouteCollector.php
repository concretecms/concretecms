<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Filesystem\FileLocator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class RouteCollector
{

    /** @var \Symfony\Component\Routing\RequestContext The context for the router */
    protected $requestContext;

    /** @var \Concrete\Core\Application\Application An application instance */
    protected $app;

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
     * @var \Concrete\Core\Http\Middleware\MiddlewareInterface[][]
     */
    protected $middlewares = [];

    /**
     * Regular expressions that lock down URL parameters to certain conditions.
     */
    protected $requirements = [];

    /**
     * @@var string[] The scopes allowed by this route group
     */
    protected $scopes = [];

    public function __construct(RequestContext $context, Application $application)
    {
        $this->requestContext = $context;
        $this->app = $application;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param string|\Concrete\Core\Http\Middleware\MiddlewareInterface $middleware
     * @return $this
     */
    public function addMiddleware($middleware, $priority = 10)
    {
        // Set up the priority slot if it doesn't exist
        if (!isset($this->middlewares[$priority])) {
            $this->middlewares[$priority] = [];
        }


        // Append the middleware
        $this->middlewares[$priority][] = $middleware;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param string[] $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function addScope($scope)
    {
        $this->scopes[] = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        // first, normalize the namespace
        $namespace = trim($namespace, '\\');
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param array $requirements
     * @return $this
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
        return $this;
    }

    /**
     * Prepend the prefix to a route
     *
     * @param \Concrete\Core\Routing\Route $route
     */
    protected function processPrefix(Route $route)
    {
        if ($this->prefix) {
            $path = '/' . trim($this->prefix, '/') . $route->getPath();
            $route->setPath($path);
        }
    }

    /**
     * Merge requirements into route
     *
     * @param \Concrete\Core\Routing\Route $route
     */
    protected function processRequirements(Route $route)
    {
        if ($this->requirements) {
            $route->setRequirements(array_merge($route->getRequirements(), $this->requirements));
        }
    }

    /**
     * Add our middlewares to the route
     *
     * @param \Concrete\Core\Routing\Route $route
     */
    protected function processMiddlewares(Route $route)
    {
        // Loop over each priority block
        foreach ($this->middlewares as $priority => $middlewareBlock) {
            foreach ($middlewareBlock as $middleware) {
                // Add the middlewares
                $route->addMiddleware($middleware, $priority);
            }
        }
    }

    /**
     * Add our middlewares to the route
     *
     * @param \Concrete\Core\Routing\Route $route
     */
    protected function processScopes(Route $route)
    {
        // Add scopes to the route
        foreach ($this->scopes as $scope) {
            $route->addScope($scope);
        }
    }

    /**
     * @param string|callable $resolver
     * @param null $pkgHandle
     * @return \Generator|\Concrete\Core\Routing\Route[]
     */
    public function routes($resolver, $pkgHandle = null)
    {
        // First, create a new, empty router for use with the routes passed in the callable.
        $router = clone $this->app->make(Router::class);
        $router->clear();

        if (is_callable($resolver)) {
            // Run the callable with our empty router.
            $resolver($router, $this);
            // Grab the routes from the router, and pass them to our route group builder.
        } elseif ($resolver instanceof RouteProviderInterface) {
            $resolver->registerRoutes($router, $this);
        } else {
            if (is_string($resolver)) {
                $this->routeFile($resolver, $router, $pkgHandle);
            } else {
                throw new \InvalidArgumentException(t('Invalid input passed to RouteGroupBuilder::routes, a resolver must be a callable or a string'));
            }
        }

        // Return the routes with our information embedded in them
        return $this->processRoutes($router->getList());
    }

    /**
     * @param string $path
     * @param \Concrete\Core\Routing\RouterInterface $router
     * @param string|null $pkgHandle
     * @throws \InvalidArgumentException
     */
    protected function routeFile($path, RouterInterface $router, $pkgHandle)
    {
        /** @var FileLocator $locator */
        $locator = $this->app->make(FileLocator::class);

        if ($pkgHandle) {
            $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
        }

        // See if we can find a matching record
        $file = $locator->getRecord(DIRNAME_ROUTES . DIRECTORY_SEPARATOR . $path);

        // If we can't find the file, throw an exception
        if (!$file->exists()) {
            throw new \InvalidArgumentException(t('Invalid input passed to RouteGroupBuilder::routes, a resolver must be a callable or a string'));
        }

        // Load the file
        $this->loadFile($file->getFile(), $router);
    }

    /**
     * Load a file with a router in scope
     * @param $path
     * @param \Concrete\Core\Routing\RouterInterface $router
     */
    protected function loadFile($path, RouterInterface $router)
    {
        // Load in a file with limited scope items
        require $path;
    }

    /**
     * Process the routes associated with a collection
     *
     * @param \Symfony\Component\Routing\RouteCollection $routes
     * @return \Generator|\Concrete\Core\Routing\Route[]
     */
    protected function processRoutes(RouteCollection $routes)
    {
        foreach ($routes as $key => $route) {
            // Apply group specific stuff
            $this->processMiddlewares($route);
            $this->processPrefix($route);
            $this->processRequirements($route);
            $this->processScopes($route);

            // Yield out the updated route
            yield $key => $route;
        }
    }
}
