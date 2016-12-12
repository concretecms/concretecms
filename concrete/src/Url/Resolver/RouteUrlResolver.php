<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteUrlResolver
 * @package Concrete\Core\Url\Resolver
 * @deprecated Use RouterUrlResolver instead.
 */
class RouteUrlResolver implements UrlResolverInterface
{

    protected $generator;
    protected $routeList;

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
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @return RouteCollection
     */
    public function getRouteList()
    {
        return $this->routeList;
    }

    /**
     * Resolve urls from the list of registered routes takes a string
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
     *                         "route/" for it to be tested.
     * @param \League\URL\URLInterface $resolved
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if (count($arguments) < 3) {
            $route_handle = array_shift($arguments);
            $route_parameters = count($arguments) ? array_shift($arguments) : array();

            if (is_string($route_handle) &&
                strtolower(substr($route_handle, 0, 6)) == 'route/' &&
                is_array($route_parameters)) {

                $route_handle = substr($route_handle, 6);
                if ($route = $this->getRouteList()->get($route_handle)) {
                    if ($path = $this->getGenerator()->generate($route_handle, $route_parameters, UrlGenerator::ABSOLUTE_PATH)) {
                        return $this->pathUrlResolver->resolve(array($path));
                    }
                }
            }
        }

        return $resolved;
    }

}
