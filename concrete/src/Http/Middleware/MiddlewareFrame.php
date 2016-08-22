<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Request;

final class MiddlewareFrame implements FrameInterface
{

    /**
     * @var \Concrete\Core\Http\Middleware\MiddlewareInterface
     */
    private $middleware;

    /**
     * @var \Concrete\Core\Http\Middleware\FrameInterface
     */
    private $lastFrame;

    public function __construct(MiddlewareInterface $middleware, FrameInterface $lastFrame)
    {
        $this->middleware = $middleware;
        $this->lastFrame = $lastFrame;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param \Concrete\Core\Http\Request $request
     * @return Response
     */
    public function next(Request $request)
    {
        return $this->middleware->process($request, $this->lastFrame);
    }

}
