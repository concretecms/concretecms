<?php

namespace Concrete\Core\Http;

use Concrete\Core\Http\Middleware\MiddlewareInterface;

class DefaultServer implements ServerInterface
{

    /** @var array[] A list of lists of middlewares ordered by priority*/
    protected $middleware = [];

    /** @var callable */
    protected $dispatcher;

    /**
     * Server constructor.
     * @param DispatcherInterface $dispatcher
     * @param array $middleware
     */
    public function __construct(DispatcherInterface $dispatcher, array $middleware=[])
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Set the dispatcher this server uses.
     * A dispatcher is used to handle the final conversion from request to response
     * function ($request, $response) : Response;
     * @param DispatcherInterface $dispatcher
     * @return self
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Add a middlware callable to the stack
     * Middleware are callables that get an opportunity to do stuff with the request during handling.
     * @param MiddlewareInterface $middleware
     * @param int $priority Lower priority runs first
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware, $priority = 10)
    {
        if (!isset($this->middleware[$priority])) {
            $this->middleware[$priority] = [];
        }

        $this->middleware[$priority][] = $middleware;
        return $this;
    }

    /**
     * Handle a request and return a response
     * @param \Concrete\Core\Http\Request $request
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        $stack = $this->getStack();
        return $stack($request);
    }

    /**
     * Reduce middleware into a stack of functions that each call the next
     * @return callable
     */
    private function getStack()
    {
        $processed = [];

        foreach ($this->middlewareGenerator() as $middleware) {
            $processed[] = $middleware;
        }

        $middleware = array_reverse($processed);
        $dispatcher = $this->dispatcher;
        $stack = array_reduce($middleware, $this->getZipper(), function($request) use ($dispatcher) {
            return $dispatcher->dispatch($request);
        });

        return $stack;
    }

    /**
     * Get the function used to zip up the middleware
     * This function runs as part of the array_reduce routine and returns a function that facilitates the middleware flow
     * @return callable
     */
    private function getZipper()
    {
        return function($last, MiddlewareInterface $middleware) {
            return function($request) use ($last, $middleware) {
                return $middleware->process($request, $last);
            };
        };
    }

    /**
     * Get a generator that converts the stored priority array into a sorted flat list
     * @return \Generator
     */
    private function middlewareGenerator()
    {
        $middlewares = $this->middleware;
        ksort($middlewares);

        foreach ($middlewares as $priorityGroup) {
            foreach ($priorityGroup as $middleware) {
                yield $middleware;
            }
        }
    }

}
