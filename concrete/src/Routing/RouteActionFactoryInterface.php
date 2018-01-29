<?php
namespace Concrete\Core\Routing;

use Symfony\Component\Routing\RouteCollection;

interface RouteActionFactoryInterface
{

    public function createAction(Route $route);

}
