<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Middleware for applying state changes to the application
 * @package Concrete\Core\Http\Middleware
 */
class ApplicationMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * Apply the request instance to the request singleton
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        \Concrete\Core\Http\Request::setInstance($request);
        $this->app->instance('Concrete\Core\Http\Request', $request);

        return $frame->next($request);
    }

}
