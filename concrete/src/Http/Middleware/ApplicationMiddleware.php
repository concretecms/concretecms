<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Request;

/**
 * Middleware for applying state changes to the application
 * @package Concrete\Core\Http\Middleware
 */
class ApplicationMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function process(Request $request, DelegateInterface $frame)
    {
        Request::setInstance($request);
        $this->app->instance('Concrete\Core\Http\Request', $request);

        return $frame->next($request);
    }

}
