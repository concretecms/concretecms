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

    public function authorize() {
        /** @var \OAuth2\Response $response */
        $response = $this->getServer()->handleAuthorizeRequest(
            \OAuth2\Request::createFromGlobals(),
            new \OAuth2\Response, ($_POST['authorized'] === 'yes'));

        $response->send();

        \Core::shutdown();
    }

    public function token()
    {
        $this->getServer()->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
        \Core::shutdown();
    }

    public function userInfo()
    {
        $request = Request::createFromGlobals();
        $server = $this->getServer();

        $response = (object) array('error' => null, 'message' => null, 'payload' => null);

        if ($server->verifyResourceRequest($request)) {
            $token = $server->getAccessTokenData($request);

            $ui = \UserInfo::getByUserName($token['user_id']);
            $response->payload = array(
                'id' => (string) $ui->getUserID(),
                'username' => (string) $ui->getUserName(),
                'displayName' => (string) $ui->getUserDisplayName()
            );
        } else {
            $response->error = 401;
            $response->message = "Not Authorized";
        }

        $json_response = new JsonResponse($response, $response->error > 0 ? $response->error : 200);
        $json_response->send();
    }

}
