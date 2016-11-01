<?php

namespace Concrete\Core\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;

/**
 * A middleware delegate for running the next delegate
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

    public function __construct(MiddlewareInterface $middleware, DelegateInterface $nextDelegate)
    {
        $this->middleware = $middleware;
        $this->nextDelegate = $nextDelegate;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param Request $request
     * @return Response
     */
    public function next(Request $request)
    {
        return $this->middleware->process($request, $this->nextDelegate);
    }

}
