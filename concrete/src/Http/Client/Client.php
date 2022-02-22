<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareTrait;
use GuzzleHttp\Client as GuzzleHttpClient;

use Psr\Http\Message\RequestInterface;
use Concrete\Core\Logging\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Exception;
use Throwable;

class Client extends GuzzleHttpClient implements LoggerAwareInterface
{

    use LoggerAwareTrait;
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

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_NETWORK;
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


    public function send(RequestInterface $request, array $options = [])
    {
        $response = parent::send($request, $options);
        $logger = $this->getLogger();
        if ($logger !== null) {
            $statusCode = $response->getStatusCode();
            try {
                $body = $response->getBody();
            } catch (Exception $x) {
                $body = '';
            } catch (Throwable $x) {
                $body = '';
            }
            if (mb_strlen($body) <= 200) {
                $shortBody = $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }
            $headers = $response->getHeaders();
            $logger->debug(
                'The response code was {statusCode} and the body was {shortBody}',
                [
                    'statusCode' => $statusCode,
                    'headers' => is_object($headers) ? $headers->toArray() : $headers,
                    'shortBody' => $shortBody,
                    'body' => $body,
                ]
            );
        }
        return $response;
    }

    public function request($method, $uri = '', array $options = [])
    {
        $logger = $this->getLogger();
        if ($logger !== null) {
            $body = '';
            if (isset($options['body'])) {
                $body = $options['body'];
            }
            $uriString = (string) $uri;
            if (mb_strlen($body) <= 200) {
                $shortBody = (string) $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }
            $headers = null;
            if (isset($options['headers'])) {
                $headers = $options['headers'];
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
        return parent::request($method, $uri, $options);

    }

}
