<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;
use Symfony\Component\Routing\Generator\UrlGenerator;

class RouteUrlResolver implements UrlResolverInterface
{

    /**
     * Resolve url's from any type of input
     *
     * This method MUST either return a `\League\URL\URL` when a url is resolved
     * or null when a url cannot be resolved.
     *
     * @param array $arguments A list of the arguments
     * @param \League\URL\URLInterface $resolved
     * @return \League\URL\URLInterface
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if (count($arguments) < 3) {
            $route_handle = array_shift($arguments);
            $route_parameters = count($arguments) ? array_shift($arguments) : array();

            if (is_string($route_handle) && is_array($route_parameters)) {
                if ($route = \Route::getList()->get($route_handle)) {
                    if ($url = \Route::getGenerator()->generate($route_handle, $route_parameters, UrlGenerator::ABSOLUTE_PATH)) {
                        $canonical = \Core::make('url/canonical');

                        return $canonical->setPath($url);
                    }
                }
            }
        }

        return $resolved;
    }

}
