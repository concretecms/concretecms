<?php

namespace Concrete\Core\API\Routing;

use Concrete\Core\API\OAuth\Controller;
use Concrete\Core\HTTP\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Routing\AbstractRouteProvider;

class OAuthRouteProvider extends AbstractRouteProvider
{

    /**
     * Register routes
     * @return void
     */
    public function register()
    {
        // Setup this route collection
        $this->addMiddleware(OAuthErrorMiddleware::class);

        // Add a route to handle oauth token generation
        $this->post('/token', [Controller::class, 'token']);
    }
}
