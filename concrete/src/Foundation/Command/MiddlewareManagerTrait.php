<?php

namespace Concrete\Core\Foundation\Command;

use function array_flatten;
use function is_callable;
use League\Tactician\Middleware;
use RuntimeException;

trait MiddlewareManagerTrait
{

    protected $middleware = [];

    /**
     * Get a list of middlewares
     *
     * @return Middleware[]
     */
    public function getMiddleware()
    {
        $middleware = $this->middleware;
        ksort($middleware);

        return array_map([$this, 'inflateMiddleware'], array_flatten($middleware, 1));
    }

    /**
     * Inflate middlware from string to a class using the applicaton
     *
     * @param string|Middleware $middleware
     *
     * @return \League\Tactician\Middleware
     */
    protected function inflateMiddleware($middleware): Middleware
    {
        if (is_string($middleware)) {
            $middleware = $this->app->make($middleware);
        }

        if (!$middleware instanceof Middleware && is_callable($middleware)) {
            $middleware = $this->app->call($middleware);
        }

        // The given middleware wasn't a string, callable, or an instance
        if (!$middleware instanceof Middleware) {
            throw new RuntimeException('Invalid command bus middleware provided. Must be a class name, a function, or a Middleware instance.');
        }

        return $middleware;
    }

    /**
     * Add a middleware to this bus
     *
     * @param string|Middleware $middleware
     * @param int $priority
     *
     * @return void
     */
    public function addMiddleware($middleware, int $priority = 10): void
    {
        if (!isset($this->middleware[$priority])) {
            $this->middleware[$priority] = [];
        }

        $this->middleware[$priority][] = $middleware;
    }

}
