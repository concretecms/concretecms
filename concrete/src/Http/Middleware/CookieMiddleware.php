<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $this->cookies->setRequest($request);

        /** @var Response $response */
        $response = $frame->next($request);

        $cleared = $this->cookies->getClearedCookies();
        foreach ($cleared as $cookie) {
            $response->headers->clearCookie($cookie, DIR_REL . '/');
        }

        $cookies = $this->cookies->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie, DIR_REL . '/');
        }

        return $response;
    }

}
