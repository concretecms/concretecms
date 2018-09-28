<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouterUrlResolver implements UrlResolverInterface
{
    /** @var \Concrete\Core\Routing\RouterInterface */
    protected $router;

    /** @var \Concrete\Core\Url\Resolver\PathUrlResolver */
    protected $pathUrlResolver;

    public function __construct(PathUrlResolver $path_url_resolver, RouterInterface $router)
    {
        $this->pathUrlResolver = $path_url_resolver;
        $this->router = $router;
    }

    /**
     * Get the url generator from the router.
     *
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        return $this->router->getGenerator();
    }

    /**
     * Get the RouteCollection from the router.
     *
     * @return RouteCollection
     */
    public function getRouteList()
    {
        return $this->router->getList();
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
     *                         "route/" for it to be tested.
     * @param \League\URL\URLInterface $resolved
     *
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if (count($arguments) < 3) {
            $route_handle = array_shift($arguments);
            $route_parameters = count($arguments) ? array_shift($arguments) : array();

            // If param1 is a string that starts with "route/" and param2 is an array...
            if (is_string($route_handle) &&
                strtolower(substr($route_handle, 0, 6)) == 'route/' &&
                is_array($route_parameters)) {
                $resolved = $this->resolveRoute(substr($route_handle, 6), $route_parameters);
            }
        }

        return $resolved;
    }

    /**
     * Resolve the route.
     *
     * @param $route_handle
     * @param $route_parameters
     *
     * @return $this|\League\URL\URLInterface|mixed|null
     */
    private function resolveRoute($route_handle, $route_parameters)
    {
        $list = $this->getRouteList();
        $generator = $this->getGenerator();

        if ($route = $list->get($route_handle)) {
            if ($path = $generator->generate($route_handle, $route_parameters, UrlGeneratorInterface::ABSOLUTE_PATH)) {
                return $this->pathUrlResolver->resolve(array($path));
            }
        }
    }
}
