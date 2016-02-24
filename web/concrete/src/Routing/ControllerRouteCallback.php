<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpKernel;
use Response;
use Request;

class ControllerRouteCallback extends RouteCallback
{
    /**
     * @param Request $request
     * @param Route $route
     * @param array $parameters
     *
     * @return Response
     */
    public function execute(Request $request, Route $route, $parameters)
    {
        $resolver = Application::make('Concrete\Core\Controller\ApplicationAwareControllerResolver');
        $callback = $resolver->getController($request);
        $arguments = $resolver->getArguments($request, $callback);
        $controller = $callback[0];
        $method = $callback[1];
        $controller->on_start();
        $response = $controller->runAction($method, $arguments);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            // note, our RedirectResponse doesn't extend Response, it extends symfony2 response
            return $response;
        }
        if ($response instanceof \Concrete\Core\View\AbstractView) {
            $content = $response->render();
        } else {
            $content = null;
            $view = $controller->getViewObject();
            if (is_object($view)) {
                $view->setController($controller);
                if (isset($view) && $view instanceof \Concrete\Core\View\AbstractView) {
                    $content = $view->render();
                }
            }
        }
        $response = new Response();
        $response->setContent($content);

        return $response;
    }

    /**
     * @return array
     */
    public static function getRouteAttributes($callback)
    {
        $attributes = array();
        $attributes['_controller'] = $callback;
        $callback = new static($callback);
        $attributes['callback'] = $callback;

        return $attributes;
    }
}
