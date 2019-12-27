<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiLoggerMiddleware
 */
class ApiLoggerMiddleware implements MiddlewareInterface, LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @return string
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_API;
    }

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        // log request headers for debugging proxies issues
        $this->logger->debug($request->headers);

        return $frame->next($request);
    }
}
