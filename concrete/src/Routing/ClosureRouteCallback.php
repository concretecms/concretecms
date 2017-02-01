<?php
namespace Concrete\Core\Routing;

use Response;
use Request;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

class ClosureRouteCallback extends RouteCallback
{
    public function execute(Request $request, \Concrete\Core\Routing\Route $route, $parameters)
    {
        $argumentsResolver = Application::make(ArgumentResolver::class);
        $arguments = $argumentsResolver->getArguments($request, $this->callback);
        $callback_response = call_user_func_array($this->callback, $arguments);

        if ($callback_response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $callback_response;
        }

        $r = new Response();
        $r->setContent($callback_response);

        return $r;
    }

    public function __sleep()
    {
        unset($this->callback);
    }

    public static function getRouteAttributes($callback)
    {
        $callback = new static($callback);

        return ['callback' => $callback];
    }
}
