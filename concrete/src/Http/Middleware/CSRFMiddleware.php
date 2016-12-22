<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for checking for a CSRF token in a http response
 * @package Concrete\Core\Http
 */
class CSRFMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Validation\CSRF\Token
     */
    private $token;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(Token $token, Repository $repository)
    {
        $this->token = $token;
        $this->config = $repository;
    }

    /**
     * Check the request to see if there is a valid CSRF token included
     * @param Request $request
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $frame
     * @return Response
     * @throws \Exception
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $enabled = $this->config->get('concrete.security.misc.csrf_middleware');
        if ($enabled && !$this->isReading($request)) {
            $header = $request->headers->get('X-CSRF-TOKEN');
            $post = $request->query->get('ccm_token');
            if (!$this->token->validate($header) && !$this->token->validate($post)) {
                throw new \Exception('Invalid token provided');
            }
        }

        /** @var Response $response */
        $response = $frame->next($request);

        return $response;
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     * Taken from Laravel
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->getMethod(), [$request::METHOD_HEAD, $request::METHOD_GET, $request::METHOD_OPTIONS]);
    }

}
