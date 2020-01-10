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

        if ($response instanceof Response) {
            return $response;
        }
        $r = new Response();
        if (is_string($response)) {
            $r->setContent($response);
        } else {
            $r->setContent($echoedResponse);
        }

        return $r;
    }
}
