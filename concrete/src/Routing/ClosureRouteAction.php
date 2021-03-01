<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

class ClosureRouteAction implements RouteActionInterface
{
    protected $callback;

    /**
     * ClosureRouteAction constructor.
     *
     * @param $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __sleep()
    {
        unset($this->callback);
    }

    public function execute(Request $request, Route $route, $parameters)
    {
        $resolver = new ArgumentResolver();
        $arguments = $resolver->getArguments($request, $this->callback);
        ob_start();
        $response = call_user_func_array($this->callback, $arguments);
        $echoedResponse = ob_get_contents();
        ob_end_clean();

        $r = new Response();
        if (is_scalar($response)) {
            $r->setContent($response);
        } else {
            if ($response) {
                return $response; // Someone has returned an object, closure, array, etc... so we let the middlewares handle it.
            } else {
                $r->setContent($echoedResponse);
            }
        }

        return $r;
    }
}
