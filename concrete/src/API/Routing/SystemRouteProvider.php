<?php

namespace Concrete\Core\API\Routing;

use Concrete\Core\API\Controller\V1\System;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\HTTP\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Routing\AbstractRouteProvider;

class SystemRouteProvider extends AbstractRouteProvider
{

    /**
     * Register routes
     * @return void
     */
    public function register()
    {
        $this->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class)
            ->addMiddleware(FractalNegotiatorMiddleware::class);

        $this->get('/info', [System::class, 'info']);
        $this->get('/status/queue', [System::class, 'queueStatus']);
    }
}
