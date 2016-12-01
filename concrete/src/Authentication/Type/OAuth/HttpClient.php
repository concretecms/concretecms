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
        $request = $this->client->getRequest();
        $request
            ->setUri($endpoint->getAbsoluteUri())
            ->setMethod($method)
            ->getHeaders()->addHeaders($extraHeaders);
        if (is_array($requestBody)) {
            $request->getPost()->fromArray($requestBody);
        } else {
            $request->setContent($requestBody);
        }
        $response = $this->client->send($request);

        return $response->getBody();
    }
}

