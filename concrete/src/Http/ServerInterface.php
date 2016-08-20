<?php

namespace Concrete\Core\Http;

interface ServerInterface
{

    /**
     * Set the dispatcher this server uses.
     * A dispatcher is used to handle the final conversion from request to response
     * function ($request, $response) : Response;
     * @param callable $dispatcher
     * @return self
     */
    public function setDispatcher(DispatcherInterface $dispatcher);

    /**
     * Add a middlware callable to the stack
     * Middleware are callables that get an opportunity to do stuff with the request during handling.
     * @param callable $middleware
     * @param int $priority Lower priority runs first
     * @return self
     */
    public function addMiddleware(callable $middleware, $priority = 10);

    /**
     * Handle a request and return a response
     * @param \Concrete\Core\Http\Request $request
     * @return Response
     */
    public function handleRequest(Request $request);

}
