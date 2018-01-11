<?php
namespace Concrete\Core\API;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\ClosureRouteCallback;
use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\User\Event\User;
use Symfony\Component\HttpFoundation\Request;

class APIServiceProvider extends ServiceProvider
{

    private $routePath = '/ccm/api/v1';

    private function handler($class, $method)
    {
        $app = $this->app;

        // Standard request handler for all routes
        return function ($parameters) use ($class, $method, $app) {
            $controller = $app->make($class);
            return $app->call([$controller, $method], $parameters);
        };
    }

    public function register()
    {
        $router = $this->app->make(Router::class);
        $this->route($router, '/site/list', $this->handler(User::class, 'user'), ['GET']);
    }

    /**
     * Register a standard route and route it through our server
     */
    private function route(RouterInterface $router, $routeTo, callable $handler, array $methods, $oauthScope = 'library')
    {
        /** @var \Concrete\Core\Routing\Route $route */
        $route = $router->register($this->routePath . $routeTo, function () {
        }, null, [], [], '', [], $methods);

        $route->setOption('oauth_scope', $oauthScope);

        $route->setDefault('callback',
            new ClosureRouteCallback(function (Request $request, $route, $parameters) use ($handler) {
                $request->attributes->set('route', $route);
                return $this->getServer($handler, $parameters)->handleRequest($request);
            }));

        return $route;
    }

}