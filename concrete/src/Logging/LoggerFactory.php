<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Application\Application;
use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Concrete\Core\Logging\Configuration\ConfigurationInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoggerFactory
{

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var ConfigurationInterface
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



