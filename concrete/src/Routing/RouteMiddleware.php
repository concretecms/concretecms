<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Http\Middleware\MiddlewareInterface;

class RouteMiddleware
{


    /**
     * @var MiddlewareInterface
     */
    protected $middleware;


    /**
     * @var int
     */
    protected $priority = 10;

    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param string $middlewareClassName
     */
    public function setMiddleware($middlewareClassName)
    {
        $this->middleware = $middlewareClassName;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }



}
