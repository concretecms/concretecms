<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Cookie\ResponseCookieJar;
use Symfony\Component\HttpFoundation\Request;

/**
 * Middleware for adding and deleting cookies to an http response.
 *
 * @package Concrete\Core\Http
 */
class CookieMiddleware implements MiddlewareInterface
{
    /**
     * @var \Concrete\Core\Cookie\ResponseCookieJar
     */
    private $responseCookies;

    /**
     * @param \Concrete\Core\Cookie\ResponseCookieJar $responseCookiess
     */
    public function __construct(ResponseCookieJar $responseCookiess)
    {
        $this->responseCookiess = $responseCookiess;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Http\Middleware\MiddlewareInterface::process()
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $this->cookies->setRequest($request);

        $response = $frame->next($request);

        $cleared = $this->responseCookies->getClearedCookies();
        foreach ($cleared as $cookie) {
            $response->headers->clearCookie($cookie, DIR_REL . '/');
        }

        $cookies = $this->responseCookies->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
