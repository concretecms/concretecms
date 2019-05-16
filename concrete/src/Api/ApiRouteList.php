<?php

namespace Concrete\Core\Api;

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
            ->setPrefix('/ccm/api/v1')
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class)
            ->addMiddleware(FractalNegotiatorMiddleware::class);

        $api->buildGroup()
            ->scope('system:info:read')
            ->routes('api/system.php');

        $api->buildGroup()
            ->scope('site:trees:read')
            ->routes('api/site.php');

        $api->buildGroup()
            ->scope('account:read')
            ->routes('api/account.php');
    }
}
