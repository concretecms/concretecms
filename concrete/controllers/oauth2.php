<?php

namespace Concrete\Controller;

use OAuth2\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Oauth2 extends \Controller
{

    /**
     * @return \OAuth2\Server
     */
    protected function getServer()
    {
        return \Core::make('oauth2/server');
    }

    public function token()
    {
        $this->getServer()->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
        \Core::shutdown();
    }


}
