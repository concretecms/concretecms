<?php

namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Logging\Channels;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Concrete\Core\Logging\Processor\Concrete5UserProcessor;

abstract class SimpleConfiguration implements ConfigurationInterface
{
    /**
     * The logging level to care about for all core logs.
     *
     * @var int
     */
    protected $coreLevel;

    /**
     * @param int $coreLevel the logging level to care about for all core logs (one of the Monolog\Logger constants)
     *
     * @see \Monolog\Logger
     */
    public function __construct($coreLevel = Logger::DEBUG)
    {
        $this->coreLevel = $coreLevel;
    }

    /**
     * @return int
     */
    public function getCoreLevel()
    {
        return $this->coreLevel;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\Configuration\ConfigurationInterface::createLogger()
     */
    public function createLogger($channel)
    {
        $logger = new Logger($channel);
        $level = $this->coreLevel;
        if (!in_array($channel, Channels::getCoreChannels())) {
            $level = Logger::DEBUG;
        }

        $handler = $this->createHandler($level);

        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushProcessor(new Concrete5UserProcessor());

        return $logger;
    }

    /**
     * Create a handler for a specific log level.
     *
     * @param int $level One of the Monolog\Logger constants
     *
     * @return \Monolog\Handler\HandlerInterface
     *
     * @see \Monolog\Logger
     */
    abstract protected function createHandler($level);
}
