<?php
namespace Concrete\Core\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;

interface RouterInterface
{

    /**
     * @param Route $route
     * @return RouteActionInterface
     */
    public function resolveAction(Route $route);


    /**
     * @return RouteCollection[]
     */
    public function getRoutes();


    public function addRoute(Route $route);

    /**
     * @param Request $request
     * @return null|MatchedRoute|ResourceNotFoundException|MethodNotAllowedException
     */
    public function matchRoute(Request $request);

    public function loadRouteList(RouteListInterface $list);

}