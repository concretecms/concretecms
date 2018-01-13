<?php

namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\DispatcherDelegate;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareStack;
use Concrete\Core\Http\Middleware\StackInterface;
use Concrete\Core\Routing\Router;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Concrete\Core\Http\RouteActionDispatcher;
class DefaultServer implements ServerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var callable */
    protected $dispatcher;

    /** @var Application */
    protected $app;

    /** @var StackInterface */
    protected $stack;

    /** @var Router */
    protected $router;

    /**
     * Server constructor.
     * @param Router $router
     * @param DispatcherInterface $dispatcher
     * @param StackInterface $stack
     */
    public function __construct(Application $app, Router $router, DispatcherInterface $dispatcher, StackInterface $stack)
    {
        $this->app = $app;
        $this->router = $router;
        $this->stack = $stack;
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
     * Add a middleware to the stack
     * @param \Concrete\Core\Http\Middleware\MiddlewareInterface $middleware
     * @param int $priority
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware, $priority = 10)
    {
        $this->stack = $this->stack->withMiddleware($middleware, $priority);
        return $this;
    }

    /**
     * Remove a middleware from the stack
     * @param \Concrete\Core\Http\Middleware\MiddlewareInterface $middleware
     * @return self
     */
    public function removeMiddleware(MiddlewareInterface $middleware)
    {
        $this->stack = $this->stack->withoutMiddleware($middleware);
        return $this;
    }

    /**
     * Take a request and pass it through middleware, then return the response
     * @param SymfonyRequest $request
     * @return SymfonyResponse
     */
    public function handleRequest(SymfonyRequest $request)
    {
        try {
            $route = $this->router->matchRoute($request);
            foreach($route->getMiddlewares() as $middleware) {
                $this->addMiddleware(
                    $this->app->make($middleware->getMiddleware()),
                    $middleware->getPriority()
                );
            }
            $stack = $this->stack;
            $dispatcher = new RouteActionDispatcher($this->router, $route);
        } catch(ResourceNotFoundException $e) {
            $dispatcher = $this->dispatcher;
        }

        $stack = $this->stack;
        if ($stack instanceof MiddlewareStack) {
            $stack = $stack->withDispatcher($this->app->make(DispatcherDelegate::class, [$dispatcher]));
        }

        return $stack->process($request);
    }

}
