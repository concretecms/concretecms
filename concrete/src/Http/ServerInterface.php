<?php

namespace Concrete\Core\Http;

use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface ServerInterface
{

    /**
     * Set the dispatcher this server uses.
     * A dispatcher is used to handle the final conversion from request to response
     * function ($request, $response) : Response;
     * @param DispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(DispatcherInterface $dispatcher);

    /**
     * Add a middlware callable to the stack
     * Middleware are callables that get an opportunity to do stuff with the request during handling.
     * @param MiddlewareInterface $middleware
     * @param int $priority Lower priority runs first
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware, $priority = 10);

    /**
     * Remove a middleware
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function removeMiddleware(MiddlewareInterface $middleware);

    /**
     * Handle a request and return a response
     * @param SymfonyRequest $request
     * @return SymfonyResponse
     */
    public function handleRequest(SymfonyRequest $request);

}
