<?php
namespace Concrete\Core\Http\Client;

use Zend\Http\Client as ZendClient;
use Zend\Http\Request as ZendRequest;
use Zend\Uri\Http as ZendUriHttp;

class Client extends ZendClient
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger = null;

    /**
     * Get the currently configured logger.
     *
     * @return LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the currently configured logger.
     *
     * @param LoggerInterface|null $value
     *
     * @return static
     */
    public function setLogger(LoggerInterface $value = null)
    {
        $this->logger = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ZendClient::send()
     */
    public function send(ZendRequest $request = null)
    {
        $response = parent::send($request);
        $logger = $this->getLogger();
        if ($logger !== null) {
            $logger->logResponse($response->getStatusCode(), $response->getHeaders()->toArray(), $response->getBody());
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @see ZendClient::doRequest()
     */
    protected function doRequest(ZendUriHttp $uri, $method, $secure = false, $headers = [], $body = '')
    {
        $logger = $this->getLogger();
        if ($logger !== null) {
            $logger->logRequest((string) $uri, $method, is_array($headers) ? $headers : (array) $headers, $body);
        }

        return parent::doRequest($uri, $method, $secure, $headers, $body);
    }
}
