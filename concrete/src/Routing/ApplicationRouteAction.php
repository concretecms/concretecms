<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;

class ApplicationRouteAction implements RouteActionInterface
{

    protected $app;

    protected $callback;

    /**
     * ApplicationRouteCallback constructor.
     */
    public function __construct(Application $app, $callback)
    {
        $this->app = $app;
        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param mixed $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }



    public function execute(Request $request, Route $route, $parameters)
    {
        // Call the resolved object method
        return $this->app->call($this->callback, [$request, $route]);
    }

}
