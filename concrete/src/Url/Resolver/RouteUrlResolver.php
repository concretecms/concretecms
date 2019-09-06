<?php

namespace Concrete\Core\Url\Resolver;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteUrlResolver.
 *
 * \@package Concrete\Core\Url\Resolver
 *
 * @deprecated use RouterUrlResolver instead
 */
class RouteUrlResolver implements UrlResolverInterface
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $generator;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routeList;

    /**
     * @var \Concrete\Core\Url\Resolver\UrlResolverInterface
     */
    protected $pathUrlResolver;

    public function __construct(UrlResolverInterface $path_url_resolver,
                                UrlGeneratorInterface $generator,
                                RouteCollection $route_list)
    {
        $this->pathUrlResolver = $path_url_resolver;
        $this->generator = $generator;
        $this->routeList = $route_list;
    }

    /**
     * @return \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteList()
    {
        return $this->routeList;
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

            if (is_string($route_handle) &&
                strtolower(substr($route_handle, 0, 6)) == 'route/' &&
                is_array($route_parameters)) {
                $route_handle = substr($route_handle, 6);
                if ($this->getRouteList()->get($route_handle)) {
                    if ($path = $this->getGenerator()->generate($route_handle, $route_parameters, UrlGeneratorInterface::ABSOLUTE_PATH)) {
                        return $this->pathUrlResolver->resolve([$path]);
                    }
                }
            }
        }

        return $resolved;
    }
}
