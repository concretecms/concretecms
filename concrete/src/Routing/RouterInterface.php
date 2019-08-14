<?php

namespace Concrete\Core\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

/**
 * @since 5.7.5.4
 */
interface RouterInterface
{
    /**
     * @param \Concrete\Core\Routing\Route $route
     *
     * @return \Concrete\Core\Routing\RouteActionInterface
     * @since 8.5.0
     */
    public function resolveAction(Route $route);

    /**
     * @return \Symfony\Component\Routing\RouteCollection[]
     * @since 8.5.0
     */
    public function getRoutes();

    /**
     * @param \Concrete\Core\Routing\Route $route
     * @since 8.5.0
     */
    public function addRoute(Route $route);

    /**
     * Get a route given its path.
     *
     * @param string $path the path to be looked for
     * @param \Symfony\Component\Routing\RequestContext $context the context to be used to match the routes
     * @param array $routeAttributes [output] if specified, this argument will contain the route attributes
     *
     * @throws \Symfony\Component\Routing\Exception\NoConfigurationException If no routing configuration could be found
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException If the resource could not be found
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException If the resource was found but the request method is not allowed
     *
     * @return \Concrete\Core\Routing\Route
     * @since 8.5.0
     */
    public function getRouteByPath($path, RequestContext $context, array &$routeAttributes = []);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Symfony\Component\Routing\Exception\NoConfigurationException If no routing configuration could be found
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException If the resource could not be found
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException If the resource was found but the request method is not allowed
     *
     * @return \Concrete\Core\Routing\MatchedRoute
     * @since 8.5.0
     */
    public function matchRoute(Request $request);

    /**
     * @since 8.5.0
     */
    public function loadRouteList(RouteListInterface $list);
}
