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
        // We have to trim the leading backslash because sometimes routes are defined starting with a backslash,
        // But they'll NEVER be registered in the container starting with one.
        // Note: this probably should be done within the router so it's all normalized throughout, but that sounds
        // like a bigger change.
        $callback[0] = ltrim($callback[0], '\\');
        $callback[0] = $this->app->make($callback[0]);
        // Call the resolved object method
        $argumentsResolver = $this->app->make(ArgumentResolver::class);
        $arguments = $argumentsResolver->getArguments($request, $callback);
        // Note: this no longer works, because Laravel needs you to pass the key of the arguments directly.
        //return $this->app->call($callback, $arguments);
        return call_user_func_array($callback, $arguments);
    }

}
