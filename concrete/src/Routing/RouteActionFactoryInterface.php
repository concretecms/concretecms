<?php
namespace Concrete\Core\Routing;

use Symfony\Component\Routing\RouteCollection;

/**
 * @since 8.5.0
 */
interface RouteActionFactoryInterface
{

    public function createAction(Route $route);

}
