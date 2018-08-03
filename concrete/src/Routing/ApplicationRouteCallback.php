<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;

class ApplicationRouteCallback extends RouteCallback
{

    protected $app;

    /**
     * ApplicationRouteCallback constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function execute(Request $request, Route $route, $parameters)
    {
        // Call the resolved object method
        return $this->app->call($this->callback, [$request, $route]);
    }

    /**
     * Return the route attribute information
     * @param $callback
     * @return array
     */
    public function getRouteAttributes($callback)
    {
        if (is_string($callback) && strpos($callback, '::')) {
            // Explode the string by ::
            $callback = explode('::', $callback, 2);
        }

        // If our first parameter is a string, inflate it
        if (is_string($callback[0])) {
            $callback[0] = $this->app->make($callback[0]);
        }

        $this->callback = $callback;
        return ['callback' => $this];
    }
}
