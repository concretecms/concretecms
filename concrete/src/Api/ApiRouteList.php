<?php

namespace Concrete\Core\Api;

use Concrete\Core\Http\Middleware\ApiLoggerMiddleware;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\Http\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class ApiRouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()->addMiddleware(OAuthErrorMiddleware::class)
            ->routes( 'api/oauth2.php');

        $api = $router->buildGroup()
            ->setPrefix('/ccm/api/1.0')
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class)
            ->addMiddleware(FractalNegotiatorMiddleware::class);

        // The ApiLoggerMiddleware needs to have high priority than the OAuthAuthenticationMiddleware
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        if ($app->make('config')->get('concrete.log.api')) {
            $api->addMiddleware(ApiLoggerMiddleware::class, 9);
        }

        $api->routes('api/system.php');
        $api->routes('api/site.php');
        $api->routes('api/account.php');
        $api->routes('api/files.php');
        $api->routes('api/users.php');
        $api->routes('api/groups.php');
        $api->routes('api/pages.php');
        $api->routes('api/areas.php');
        $api->routes('api/versions.php');
        $api->routes('api/blocks.php');
        $api->routes('api/express.php');
    }
}
