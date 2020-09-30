<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Application\Application;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Concrete\Core\Logging\Configuration\ConfigurationInterface;
use Psr\Log\NullLogger;

class LoggerFactory
{
    /**
     * @var \Concrete\Core\Events\EventDispatcher
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

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(ConfigurationFactory $configurationFactory, EventDispatcher $dispatcher, Application $app)
    {
        $this->configurationFactory = $configurationFactory;
        $this->dispatcher = $dispatcher;
        $this->config = $this->configurationFactory->createConfiguration();
        $this->app = $app;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->config;
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
        if (!$this->app->isInstalled()) {
            return new NullLogger();
        }
        $logger = $this->config->createLogger($channel);
        $event = new Event($logger);
        $this->dispatcher->dispatch('on_logger_create', $event);

        return $logger;
    }
}
