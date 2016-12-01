<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Logging\Logger as CoreLogger;

class Logger implements LoggerInterface
{
    /**
     * @var CoreLogger
     */
    protected $logger;

    /**
     * @param CoreLogger $logger
     */
    public function __construct(CoreLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @see LoggerInterface::logRequest()
     */
    public function logRequest($uri, $method, array $headers, $body)
    {
        $message = "# REQUEST:\n";
        $message .= "\tURI: $uri\n";
        $message .= "\tMethod: $method\n";
        $message .= "\tHeaders: \n";
        if (!empty($headers)) {
            $keyValues = [];
            foreach ($headers as $name => $value) {
                $keyValues[] = "$name: $value";
            }
            $message .= "\t\t" . implode("\n\t\t", $keyValues) . "\n";
        }
        $message .= "\tBody: $body\n";

        $this->logger->addDebug($message);
    }

    /**
     * {@inheritdoc}
     *
     * @see LoggerInterface::logResponse()
     */
    public function logResponse($statusCode, array $headers, $body)
    {
        $message = "# RESPONSE:\n";
        $message .= "\tStatus: $statusCode\n";
        $message .= "\tHeaders: \n";
        if (!empty($headers)) {
            $keyValues = [];
            foreach ($headers as $name => $value) {
                $keyValues[] = "$name: $value";
            }
            $message .= "\t\t" . implode("\n\t\t", $keyValues) . "\n";
        }
        $message .= "\tBody: $body\n";
        $this->logger->addDebug($message);
    }
}
