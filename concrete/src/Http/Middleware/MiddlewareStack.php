<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\MiddlewareFrame;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The default stack used to keep track of middleware and process requests
 * @package Concrete\Core\Http\Middleware
 */
final class MiddlewareStack implements StackInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var array[] A list of lists of middlewares ordered by priority*/
    protected $middleware = [];

    /**
     * @var \Concrete\Core\Http\Middleware\DispatcherFrame|Mock_BlockController_7fcd43c1|Mock_TrackableBlockController_a75af608
     */
    protected $dispatcher;

    /**
     * MiddlewareStack constructor.
     * Dispat
     */
    public function __construct(DelegateInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get a stack with the given dispatcher
     * @param \Concrete\Core\Http\Middleware\DelegateInterface $dispatcher
     * @return self
     */
    public function withDispatcher(DelegateInterface $dispatcher)
    {
        $stack = clone $this;

        $stack->dispatcher = $dispatcher;
        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function withMiddleware(MiddlewareInterface $middleware, $priority = 10)
    {
        $stack = clone $this;
        if (!isset($stack->middleware[$priority])) {
            $stack->middleware[$priority] = [];
        }

        $stack->middleware[$priority][] = $middleware;
        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function withoutMiddleware(MiddlewareInterface $middleware)
    {
        $stack = clone $this;

        $stack->middleware = array_map(function($priorityGroup) use ($middleware) {
            return array_map(function($stackMiddleware) use ($middleware)  {
                return $middleware === $stackMiddleware ? null : $stackMiddleware;
            }, $priorityGroup);
        }, $stack->middleware);

        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function process(Request $request)
    {
        $stack = $this->getStack();
        return $stack->next($request);
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
        $stack = array_reduce($middleware, $this->getZipper(), $this->dispatcher);

        return $stack;
    }

    /**
     * Get the function used to zip up the middleware
     * This function runs as part of the array_reduce routine and reduces the list of middlewares into a single delegate
     * @return callable
     */
    private function getZipper()
    {
        $app = $this->app;
        return function($last, MiddlewareInterface $middleware) use ($app) {
            return $app->make(DelegateInterface::class, [$middleware, $last]);
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
