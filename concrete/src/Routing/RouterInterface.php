<?php

namespace Concrete\Core\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

interface RouterInterface
{
    /**
     * @param \Concrete\Core\Routing\Route $route
     *
     * @return \Concrete\Core\Routing\RouteActionInterface
     */
    public function resolveAction(Route $route);

    /**
     * @return \Symfony\Component\Routing\RouteCollection[]
     */
    public function getRoutes();

    /**
     * @param \Concrete\Core\Routing\Route $route
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
     */
    public function matchRoute(Request $request);

    public function loadRouteList(RouteListInterface $list);
}
