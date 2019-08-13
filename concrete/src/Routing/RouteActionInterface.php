<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Http\Request;

/**
 * @since 8.5.0
 */
interface RouteActionInterface
{

    public function execute(Request $request, Route $route, $parameters);

}
