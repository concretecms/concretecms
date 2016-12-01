<?php
namespace Concrete\Core\Http\Client;

interface LoggerInterface
{
    /**
     * Log a request that is going to be sent.
     *
     * @param string $uri
     * @param string $method
     * @param array $headers
     * @param string $body
     */
    public function logRequest($uri, $method, array $headers, $body);

    /**
     * Log a received response.
     *
     * @param int $statusCode
     * @param array $headers
     * @param string $body
     */
    public function logResponse($statusCode, array $headers, $body);
}
