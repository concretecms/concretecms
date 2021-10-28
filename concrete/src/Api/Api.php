<?php
namespace Concrete\Core\Api;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\CanonicalUrlResolver;
use Frankkessler\Guzzle\Oauth2\GrantType\ClientCredentials;
use Frankkessler\Guzzle\Oauth2\GrantType\RefreshToken;
use Frankkessler\Guzzle\Oauth2\Oauth2Client;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

class Api
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var APIFactory
     */
    protected $factory;

    public function __construct(APIFactory $factory, $baseUrl, $config)
    {
        $this->factory = $factory;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    protected function getServiceClient($name)
    {
        return new APIClient($this->getHTTPClient(), $this->getDescription($name));
    }

    protected function getDescription($name)
    {
        $config = $this->factory->getDescriptionConfig($name);
        $config['baseUrl'] = $this->getBaseUrl() . $config['baseUrl'];
        return new Description($config);
    }

    public function setHttpPClient($client)
    {
        $this->client = $client;
    }

    public function getHttpClient()
    {
        if (!isset($this->client)) {
            $this->client = new Oauth2Client([
                'base_uri' => $this->baseUrl,
                'auth' => 'oauth2',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $config = [
                'client_id' => $this->config['credentials']['client_id'],
                'client_secret' => $this->config['credentials']['client_secret'],
                'token_url' => $this->baseUrl . '/oauth/2.0/token',
                'scope' => $this->config['scope'],
            ];

            $token = new ClientCredentials($config);
            $this->client->setGrantType($token);

            $refreshToken = new RefreshToken($config);
            $this->client->setRefreshTokenGrantType($refreshToken);
        }
        return $this->client;
    }

    public function system()
    {
        return $this->getServiceClient('system');
    }

    public function site()
    {
        return $this->getServiceClient('site');
    }

    public function __call($name, $arguments)
    {
        // Handles dynamic parsing of the service client, allowing packages
        // to add to this via config.
        return $this->getServiceClient($name);
    }


}