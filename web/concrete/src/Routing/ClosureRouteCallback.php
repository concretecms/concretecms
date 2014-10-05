<?php
namespace Concrete\Core\Routing;
use Symfony\Component\HttpKernel;
use Response;
use Request;

class ClosureRouteCallback extends RouteCallback
{
    public function execute(Request $request, \Concrete\Core\Routing\Route $route, $parameters)
    {
        $resolver = new HttpKernel\Controller\ControllerResolver();
        $arguments = $resolver->getArguments($request, $this->callback);
        $r = new Response();
        $r->setContent(call_user_func_array($this->callback, $arguments));

        return $r;
    }

    public function __sleep()
    {
        unset($this->callback);
    }

    public static function getRouteAttributes($callback)
    {
        $callback = new static($callback);

        return array('callback' => $callback);
    }

}
