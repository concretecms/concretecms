<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoggerFactory
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Concrete\Core\Logging\Configuration\ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var \Concrete\Core\Logging\Configuration\ConfigurationInterface
     */
    protected $config;

    public function __construct(ConfigurationFactory $configurationFactory, EventDispatcher $dispatcher)
    {
        $this->configurationFactory = $configurationFactory;
        $this->dispatcher = $dispatcher;
        $this->config = $this->configurationFactory->createConfiguration();
    }

    /**
     * Create a new logger instance.
     *
     * @param string $channel
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function createLogger($channel)
    {
        $logger = $this->config->createLogger($channel);
        $event = new Event($logger);
        $this->dispatcher->dispatch('on_logger_create', $event);

        return $logger;
    }
}
