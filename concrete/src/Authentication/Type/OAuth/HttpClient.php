<?php
namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Http\Client\Client as CoreHttpClient;
use OAuth\Common\Http\Client\ClientInterface as OAuthClientInterface;
use OAuth\Common\Http\Uri\UriInterface as OAuthUriInterface;

class HttpClient implements OAuthClientInterface
{
    /**
     * @var CoreHttpClient
     */
    protected $client;

    /**
     * @param CoreHttpClient $client
     */
    public function __construct(CoreHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}

     * @see OAuthClientInterface::retrieveResponse()
     */
    public function retrieveResponse(OAuthUriInterface $endpoint, $requestBody, array $extraHeaders = [], $method = 'POST')
    {

        $form_params = [];
        $body = '';
        if (is_array($requestBody)) {
            $form_params = $requestBody;
        } else {
            $body = $requestBody;
        }
        $response = $this->client->request($method, $endpoint->getAbsoluteUri(), [
            'headers' => $extraHeaders,
            'form_params' => $form_params,
            'body' => $body
        ]);
        return $response->getBody()->getContents();
    }
}

