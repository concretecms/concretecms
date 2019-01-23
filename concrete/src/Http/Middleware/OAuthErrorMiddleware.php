<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Request;

class OAuthErrorMiddleware implements MiddlewareInterface
{

    /**
     * Process the request and return a PSR7 error response if needed
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        try {
            // Try returning the response normally
            return $frame->next($request);
        } catch (OAuthServerException $e) {
            // Generate an error based on the exception
            return $e->generateHttpResponse(new Response());
        }
    }
}
