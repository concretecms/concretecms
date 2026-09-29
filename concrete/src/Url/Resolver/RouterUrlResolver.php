<?php

namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class RouterUrlResolver implements UrlResolverInterface
{
    /**
     * @var \Concrete\Core\Routing\Router
     */
    protected $router;

    /**
     * @var \Concrete\Core\Url\Resolver\PathUrlResolver
     */
    protected $pathUrlResolver;

    public function __construct(PathUrlResolver $path_url_resolver, Router $router)
    {
        $this->pathUrlResolver = $path_url_resolver;
        $this->router = $router;
    }

    /**
     * Get the url generator for the routes of the router.
     *
     * @return \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    public function getGenerator()
    {
        // The generator only builds the paths of the routes: the path resolver turns them into URLs (with the base path of the site)
        return new UrlGenerator($this->router->getRoutes(), new RequestContext());
    }

    /**
     * Get the RouteCollection from the router.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteList()
    {
        return $this->router->getRoutes();
    }

    /**
     * Resolve urls from the list of registered routes takes a string.
     *
     * [code]
     * $url = \URL::to('route/user_route', array('id' => 1));
     * [/code]
     *
     * OR
     *
     * [code]
     * // Register a route
     * $route_list->register('/users/{id}', '\My\Application\User\Controller::view', 'user_route');
     *
     * // Create a resolver
     * $route_url_resolver = new \Concrete\Core\Url\Resolver\RouteUrlResolver($generator, $route_list);
     *
     * // Retrieve the URL
     * $url = $route_url_resolver->resolve(array('route/user_route', array('id' => 1)));
     * [/code]
     *
     * @param array $arguments [ string $handle, array $parameters = array() ]
     *                         The first parameter MUST be prepended with
     *                         "route/" for it to be tested
     * @param \League\URL\URLInterface|null $resolved
     *
     * @return \League\URL\URLInterface|null
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if (count($arguments) < 3) {
            $route_handle = array_shift($arguments);
            $route_parameters = count($arguments) ? array_shift($arguments) : [];

            // If param1 is a string that starts with "route/" and param2 is an array...
            if (is_string($route_handle) &&
                strtolower(substr($route_handle, 0, 6)) == 'route/' &&
                is_array($route_parameters)) {
                $url = $this->resolveRoute(substr($route_handle, 6), $route_parameters);
                if ($url !== null) {
                    $resolved = $url;
                }
            }
        }

        return $resolved;
    }

    /**
     * Resolve the route.
     *
     * @param string $route_handle
     * @param array $route_parameters
     *
     * @return \League\URL\URLInterface|null
     */
    private function resolveRoute($route_handle, $route_parameters)
    {
        $list = $this->getRouteList();
        if ($list->get($route_handle)) {
            $generator = $this->getGenerator();
            if ($path = $generator->generate($route_handle, $route_parameters, UrlGeneratorInterface::ABSOLUTE_PATH)) {
                return $this->pathUrlResolver->resolve([$path]);
            }
        }
    }
}
