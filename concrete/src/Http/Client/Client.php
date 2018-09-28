<?php
namespace Concrete\Core\Http\Client;

use Zend\Http\Client as ZendClient;
use Zend\Http\Request as ZendRequest;
use Zend\Uri\Http as ZendUriHttp;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Client extends ZendClient implements LoggerAwareInterface
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
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            if (mb_strlen($body) <= 200) {
                $shortBody = $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }
            $logger->debug(
                'The response code was {statusCode} and the body was {shortBody}',
                [
                    'statusCode' => $statusCode,
                    'headers' => $response->getHeaders()->toArray(),
                    'shortBody' => $shortBody,
                    'body' => $body,
                ]
            );
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
            $uriString = (string) $uri;
            if (mb_strlen($body) <= 200) {
                $shortBody = (string) $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }
            $logger->debug(
                'Sending {method} request to {uri} with body {shortBody}',
                [
                    'uri' => $uriString,
                    'method' => $method,
                    'headers' => is_array($headers) ? $headers : (array) $headers,
                    'shortBody' => $shortBody,
                    'body' => $body,
                ]
            );
        }

        return parent::doRequest($uri, $method, $secure, $headers, $body);
    }
}
