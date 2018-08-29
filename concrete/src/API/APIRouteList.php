<?php

namespace Concrete\Core\API;

use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\HTTP\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class APIRouteList implements RouteListInterface
{

    public function loadRoutes(Router $router)
    {

        $router->buildGroup()->addMiddleware(OAuthErrorMiddleware::class)
            ->routes( 'api/oauth2.php');

        $router->buildGroup()
            ->setPrefix('/ccm/api/v1')
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class)
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes('api/system.php')
            ->routes('api/site.php');
    }
}
