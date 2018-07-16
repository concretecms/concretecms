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
    private $responseCookieJar;

    /**
     * @param \Concrete\Core\Cookie\ResponseCookieJar $responseCookieJar
     */
    public function __construct(ResponseCookieJar $responseCookieJar)
    {
        $this->responseCookieJar = $responseCookieJar;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Http\Middleware\MiddlewareInterface::process()
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        $cleared = $this->responseCookieJar->getClearedCookies();
        foreach ($cleared as $cookie) {
            $response->headers->clearCookie($cookie, DIR_REL . '/');
        }

        $cookies = $this->responseCookieJar->getCookies();
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
