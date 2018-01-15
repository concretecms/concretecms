<?php
namespace Concrete\Core\API;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Frankkessler\Guzzle\Oauth2\GrantType\ClientCredentials;
use Frankkessler\Guzzle\Oauth2\GrantType\RefreshToken;
use Frankkessler\Guzzle\Oauth2\Oauth2Client;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

class ClientFactory
{

    protected function getEndpoint($baseUri)
    {
        return $baseUri . '/ccm/api/v1/';
    }

    public function createClient($baseUri, $clientId, $clientSecret)
    {
        $baseUri = trim($baseUri, '/');
        $client = new Oauth2Client([
            'base_uri' => $baseUri,
            'auth' => 'oauth2',
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $config = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'token_url' => $baseUri . '/oauth/2.0/token'
        ];

        $token = new ClientCredentials($config);
        $client->setGrantType($token);

//        $refreshToken = new RefreshToken($config);
  //      $client->setRefreshTokenGrantType($refreshToken);

        $description = new Description([
            'baseUrl' => $this->getEndpoint($baseUri),
            'operations' => [
                'helloWorld' => [
                    'httpMethod' => 'GET',
                    'uri' => 'hello',
                    'responseModel' => 'helloResponse',
                    'parameters' => []
                    ]
                ],
            'models' => [
                'helloResponse' => [
                    'type' => 'object',
                    'properties' => [
                        'response' => [
                            'location' => 'json',
                            'type' => 'string'
                        ]
                    ]
                ]
            ]
        ]);

        $guzzleClient = new GuzzleClient($client, $description);
        return $guzzleClient;
    }


}