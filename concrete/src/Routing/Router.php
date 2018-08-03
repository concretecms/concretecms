<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Middleware\DispatcherDelegate;
use Concrete\Core\Http\Middleware\MiddlewareStack;
use Concrete\Core\Http\RouteDispatcher;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Request;
use Loader;

class Router implements RouterInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $generator;

    /**
     * @var RequestContext|null
     */
    protected $context;

    /**
     * @var SymfonyRouteCollection
     */
    protected $collection;

    /** @var \Concrete\Core\Http\Request */
    protected $request;
    protected $themePaths = array();
    public $routes = array();

    /**
     * Clear the router
     */
    public function clear()
    {
        if (isset($this->app)) {
            $this->collection = $this->app->make(SymfonyRouteCollection::class);
        } else {
            $this->collection = new SymfonyRouteCollection();
        }
    }

    /**
     * Handle cloning the router
     */
    public function __clone()
    {
        if ($this->collection) {
            // Clone our internal collection as well
            $this->collection = clone $this->collection;
        }
    }

    /**
     * @return RequestContext
     */
    public function getContext()
    {
        if (!$this->context) {
            $this->context = new RequestContext();
            $this->context->fromRequest(\Request::getInstance());
        }

        return $this->context;
    }

    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        if (!$this->generator) {
            $this->generator = new UrlGenerator($this->getList(), $this->getContext());
        }

        return $this->generator;
    }

    /**
     * @param $generator
     */
    public function setGenerator(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Get the current route collection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getList()
    {
        if (!$this->collection) {
            $this->clear();
        }

        return $this->collection;
    }

    public function setRequest(Request $req)
    {
        $this->request = $req;
    }

    /**
     * Register a symfony route with as little as a path and a callback.
     *
     * @param string $path The full path for the route
     * @param \Closure|string $callback `\Closure` or "dispatcher" or "\Namespace\Controller::action_method"
     * @param string|null $handle The route handle, if one is not provided the handle is generated from the path "/" => "_"
     * @param array $requirements The Parameter requirements, see Symfony Route constructor
     * @param array $options The route options, see Symfony Route constructor
     * @param string $host The host pattern this route requires, see Symfony Route constructor
     * @param array|string $schemes The schemes or scheme this route requires, see Symfony Route constructor
     * @param array|string $methods The HTTP methods this route requires, see see Symfony Route constructor
     * @param string $condition see Symfony Route constructor
     *
     * @return \Symfony\Component\Routing\Route
     */
    public function register(
        $path,
        $callback,
        $handle = null,
        array $requirements = array(),
        array $options = array(),
        $host = '',
        $schemes = array(),
        $methods = array(),
        $condition = null
    ) {
        // Get the handle for this route
        $handle = $this->getHandle($path, $handle);

        // Build a route object
        $route = $this->buildRoute($path, $callback, $requirements, $options, $host, $schemes, $methods, $condition);

        $this->getList()->add($handle, $route);

        return $route;
    }

    /**
     * Build a new Route instance
     *
     * @param string $path The full path for the route
     * @param \Closure|string|array $callback `\Closure` or "dispatcher" or "\Namespace\Controller::action_method"
     * @param array $requirements The Parameter requirements, see Symfony Route constructor
     * @param array $options The route options, see Symfony Route constructor
     * @param string $host The host pattern this route requires, see Symfony Route constructor
     * @param array|string $schemes The schemes or scheme this route requires, see Symfony Route constructor
     * @param array|string $methods The HTTP methods this route requires, see see Symfony Route constructor
     * @param string $condition see Symfony Route constructor
     *
     * @return \Concrete\Core\Routing\Route
     */
    protected function buildRoute(
        $path,
        $callback,
        array $requirements = array(),
        array $options = array(),
        $host = '',
        $schemes = array(),
        $methods = array(),
        $condition = null
    ) {
        // setup up standard concrete5 pathing
        $trimmed_path = trim($path, '/');
        $path = '/' . $trimmed_path . '/';
        $attributes = null;

        if ($callback instanceof \Closure) {
            // Handle strictly closure based routes
            $attributes = ClosureRouteCallback::getRouteAttributes($callback);
        } elseif ($callback == 'dispatcher') {
            // Handle pages
            $attributes = DispatcherRouteCallback::getRouteAttributes($callback);
        } elseif (is_string($callback) || (is_array($callback) && is_string($callback[0]))) {
            $class = $callback[0];

            if (!$class instanceof Controller) {
                // Handle Application autowiring based routing
                $applicationCallback = new ApplicationRouteCallback($this->app);
                $attributes = $applicationCallback->getRouteAttributes($callback);
            }
        }

        if (!$attributes) {
            // If no other handler was set, handle the route as if it were a controller
            $attributes = ControllerRouteCallback::getRouteAttributes($callback);
        }

        // Set the path explicitly to our normalized path
        $attributes['path'] = $path;

        return new Route($path, $attributes, $requirements, $options, $host, $schemes, $methods, $condition);
    }

    /**
     * Get the handle for a route
     *
     * @param string $path
     * @param null|string $handle
     * @return string
     */
    protected function getHandle($path, $handle = null)
    {
        // If there isn't a handle, make one from the path
        if (!$handle) {
            $handle = preg_replace('/[^A-Za-z0-9\_]/', '_', $path);
            $handle = preg_replace('/\_+/', '_', $handle);
            $handle = trim($handle, '_');
        }

        return $handle;
    }

    public function registerMultiple(array $routes)
    {
        foreach ($routes as $route => $route_settings) {
            array_unshift($route_settings, $route);
            call_user_func_array(array($this, 'register'), $route_settings);
        }
    }

    public function execute(Route $route, $parameters)
    {
        // Prepare our request
        $request = $this->request;
        $request->attributes->set('_route', $route);
        $request->attributes->set('_routeParameters', $route);

        // Prepare a middleware stack with our callback at the center
        $dispatcher = $this->app->make(RouteDispatcher::class, [$route, $parameters]);
        $delegate = new DispatcherDelegate($dispatcher);
        $stack = $this->app->make(MiddlewareStack::class, [$delegate]);
        $middlewares = $route->getMiddleware();

        foreach ($middlewares as $priority => $middlewareBlock) {
            foreach ($middlewareBlock as $middleware) {

                // Inflate any strings
                if (is_string($middleware)) {
                    $middleware = $this->app->make($middleware);
                }

                $stack = $stack->withMiddleware($middleware, $priority);
            }
        }

        // Process the middleware stack and return the result
        return $stack->process($request);
    }

    /**
     * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes.
     *
     * @param $path string
     * @param $theme object, if null site theme is default
     */
    public function setThemeByRoute($path, $theme = null, $wrapper = FILENAME_THEMES_VIEW)
    {
        $this->themePaths[$path] = array($theme, $wrapper);
    }

    public function setThemesbyRoutes(array $routes)
    {
        foreach ($routes as $route => $theme) {
            if (is_array($theme)) {
                $this->setThemeByRoute($route, $theme[0], $theme[1]);
            } else {
                $this->setThemeByRoute($route, $theme);
            }
        }
    }

    /**
     * This grabs the theme for a particular path, if one exists in the themePaths array.
     *
     * @param string $path
     *
     * @return string|bool
     */
    public function getThemeByRoute($path)
    {
        // there's probably a more efficient way to do this
        $txt = Loader::helper('text');
        foreach ($this->themePaths as $lp => $layout) {
            if ($txt->fnmatch($lp, $path)) {
                return $layout;
            }
        }

        return false;
    }

    public function route($data)
    {
        if (is_array($data)) {
            $path = $data[0];
            $pkg = $data[1];
        } else {
            $path = $data;
        }

        $path = trim($path, '/');
        $pkgHandle = null;
        if ($pkg) {
            if (is_object($pkg)) {
                $pkgHandle = $pkg->getPackageHandle();
            } else {
                $pkgHandle = $pkg;
            }
        }

        $route = '/ccm';
        if ($pkgHandle) {
            $route .= "/{$pkgHandle}";
        } else {
            $route .= '/system';
        }

        $route .= "/{$path}";

        return $route;
    }

    /**
     * Register a group of routes using a callback or path resolver
     *
     * If a callable resolver is used:
     *     function(RouterInterface $router, RouteCollector $collector): void;
     *
     * If a path resolver is used, the `$router` will be in scope, and the collector will be `$this`
     *
     * @param string $prefix The prefix to give all routes in this group
     * @param string|callable $resolver Either a callable or a path to a file that registers routes
     * @param string|null $pkgHandle A package to look for the resolver in
     * @return $this|static
     */
    public function group($prefix, $resolver, $pkgHandle = null)
    {
        // Use a route collector to vaccuum up the group
        $collector = $this->app->make(RouteCollector::class, [$this->getContext()]);
        $collector->setPrefix($prefix);

        $routes = $collector->routes($resolver, $pkgHandle);

        // Append all the routes
        foreach ($routes as $name => $route) {
            $this->getList()->add($name, $route);
        }

        return $this;
    }

    /**
     * Add a simple route
     *
     * @param string|string[] $methods GET | POST | PUT | PATCH | HEAD | DELETE | OPTIONS
     * @param $path The path to the route `/path/to/route`
     * @param string|callable|array $handler
     * @param callable $factory A callable to help build the route `function(Route $route): Route`
     */
    public function to($methods, $path, $handler, $factory = null)
    {
        $handle = $this->getHandle($path);
        $route = $this->buildRoute($path, $handler, [], [], null, [], (array) $methods);

        if ($factory) {
            // Run the factory method and give it a chance to replace the route instance
            if ($result = $factory($route)) {
                $route = $result;
            }
        }

        $this->collection->add($handle, $route);
    }

    /**
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function get($path, $handler, $factory = null)
    {
        return $this->to(['GET', 'HEAD'], $path, $handler, $factory);
    }

    /**
     * Add a route that matches the POST request method
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function post($path, $handler, $factory = null)
    {
        return $this->to('POST', $path, $handler, $factory);
    }

    /**
     * Add a route that matches the PUT request method
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function put($path, $handler, $factory = null)
    {
        return $this->to('PUT', $path, $handler, $factory);
    }

    /**
     * Add a route that matches the PATCH request method
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function patch($path, $handler, $factory = null)
    {
        return $this->to('PATCH', $path, $handler, $factory);
    }

    /**
     * Add a route that matches the HEAD request method
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function head($path, $handler, $factory = null)
    {
        return $this->to('HEAD', $path, $handler, $factory);
    }

    /**
     * Add a route that matches the DELETE request method
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function delete($path, $handler, $factory = null)
    {
        return $this->to('DELETE', $path, $handler, $factory);
    }

    /**
     * Add a route that matches the OPTIONS request method
     *
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function options($path, $handler, $factory = null)
    {
        return $this->to('OPTIONS', $path, $handler, $factory);
    }

    /**
     * Add a route that matches any incoming request method
     *
     * @param $path
     * @param $handler
     * @param $factory
     */
    public function any($path, $handler, $factory = null)
    {
        return $this->to(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], $path, $handler, $factory);
    }

}
