<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Session\Storage\Handler\DatabaseSessionHandler;
use Concrete\Core\Support\Facade\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for Session start
 * @package Concrete\Core\Http\Middleware
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * Check if the sessions need to be cleared post expiry
     * @param Request $request
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {

        $handler = Config::get('concrete.session.handler');

        if ($handler == 'database') {
            $handler = new DatabaseSessionHandler();
            $handler->gc(Config::get('concrete.session.max_lifetime'));
        }

        return $frame->next($request);
    }

}
