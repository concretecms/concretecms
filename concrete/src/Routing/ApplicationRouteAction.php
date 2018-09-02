<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
class ApplicationRouteAction implements RouteActionInterface
{

    protected $app;


    /**
     * @var array
     */
    protected $callback;

    /**
     * ApplicationRouteCallback constructor.
     */
    public function __construct(Application $app, array $callback)
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
        $callback = $this->callback;
        $callback[0] = $this->app->make($callback[0]);
        // Call the resolved object method
        $argumentsResolver = $this->app->make(ArgumentResolver::class);
        $arguments = $argumentsResolver->getArguments($request, $callback);
        return $this->app->call($callback, $arguments);
    }

}
