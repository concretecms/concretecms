<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Http\Request;

interface RouteActionInterface
{

    public function execute(Request $request, Route $route, $parameters);

}
