<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Request;

interface MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, callable $next);

}
