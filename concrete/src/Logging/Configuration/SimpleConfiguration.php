<?php

namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Logging\Channels;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Concrete\Core\Logging\Processor\ConcreteUserProcessor;

abstract class SimpleConfiguration implements ConfigurationInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

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
        if (!$this->app) {
            throw new \RuntimeException('No application instance provided.');
        }

        $logger = new Logger($channel);
        $level = $this->coreLevel;
        if (!in_array($channel, Channels::getCoreChannels())) {
            $level = Logger::DEBUG;
        }

        $handler = $this->createHandler($level);

        $logger->pushHandler($handler);
        $logger->pushProcessor($this->app->make(PsrLogMessageProcessor::class));
        $logger->pushProcessor($this->app->make(ConcreteUserProcessor::class));

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
