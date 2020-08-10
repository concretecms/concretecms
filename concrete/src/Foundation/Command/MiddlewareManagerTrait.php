<?php

namespace Concrete\Core\Foundation\Command;

use function array_flatten;
use function is_callable;
use League\Tactician\Middleware;
use RuntimeException;

trait MiddlewareManagerTrait
{
    /**
     * The list of middlewares (array keys are the priority, array values are list of middleware instances or class names or callables).
     *
     * @var array[]
     */
    protected $middleware = [];

    /**
     * Get a list of middlewares.
     *
     * @throws \RuntimeException if a registered middewares is not valid
     *
     * @return \League\Tactician\Middleware[]
     */
    public function getMiddleware(): array
    {
        $middleware = $this->middleware;
        ksort($middleware);

        return array_map([$this, 'inflateMiddleware'], array_flatten($middleware, 1));
    }

    /**
     * Add a middleware to this bus.
     *
     * @param string|Middleware $middleware
     * @param int $priority
     *
     * @return $this
     */
    public function addMiddleware($middleware, int $priority = 10): object
    {
        if (!isset($this->middleware[$priority])) {
            $this->middleware[$priority] = [];
        }

        $this->middleware[$priority][] = $middleware;

        return $this;
    }

    /**
     * Inflate middlware from string to a class using the applicaton.
     *
     * @param string|\League\Tactician\Middleware|callable $middleware
     *
     * @throws \RuntimeException if $middleware is not valid
     *
     * @return \League\Tactician\Middleware
     */
    protected function inflateMiddleware($middleware): Middleware
    {
        if (is_string($middleware)) {
            $middleware = $this->app->make($middleware);
        }

        if (!$middleware instanceof Middleware) {
            if (is_callable($middleware)) {
                $middleware = $this->app->call($middleware);
            }
            if (!$middleware instanceof Middleware) {
                // The given middleware wasn't a string, callable, or an instance

                throw new RuntimeException('Invalid command bus middleware provided. Must be a class name, a function, or a Middleware instance.');
            }
        }

        return $middleware;
    }
}
