<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;

/**
 * Middleware for adding and deleting cookies to an http response
 * @package Concrete\Core\Http
 */
class CookieMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Cookie\CookieJar
     */
    private $cookies;

    public function __construct(CookieJar $cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Add or remove cookies from the
     * @param \Concrete\Core\Http\Request $request
     * @param callable $next
     * @return \Concrete\Core\Http\Response
     */
    public function process(Request $request, callable $next)
    {
        $this->cookies->setRequest($request);

        /** @var Response $response */
        $response = $next($request);

        $cleared = $this->cookies->getClearedCookies();
        foreach ($cleared as $cookie) {
            $response->headers->clearCookie($cookie);
        }

        $cookies = $this->cookies->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

}
