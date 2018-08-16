<?php

namespace Concrete\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A middleware delegate for running the next middleware
 * @package Concrete\Core\Http
 */
final class MiddlewareDelegate implements DelegateInterface
{

    /**
     * @var \Concrete\Core\Http\Middleware\MiddlewareInterface
     */
    private $middleware;

    /**
     * @var \Concrete\Core\Http\Middleware\DelegateInterface
     */
    private $nextDelegate;

    /**
     * @var \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory
     */
    private $foundationFactory;

    public function __construct(
        MiddlewareInterface $middleware,
        DelegateInterface $nextDelegate,
        HttpFoundationFactory $foundationFactory
    ) {
        $this->middleware = $middleware;
        $this->nextDelegate = $nextDelegate;
        $this->foundationFactory = $foundationFactory;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param Request $request
     * @return Response
     */
    public function next(Request $request)
    {
        $response = $this->middleware->process($request, $this->nextDelegate);

        // Negotiate PSR7 responses
        if ($response instanceof ResponseInterface) {
            return $this->foundationFactory->createResponse($response);
        }

        return $response;
    }

}
