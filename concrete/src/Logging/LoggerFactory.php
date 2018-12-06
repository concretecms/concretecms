<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Concrete\Core\Logging\Configuration\ConfigurationInterface;
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

    public function __construct(ConfigurationFactory $configurationFactory, EventDispatcher $dispatcher)
    {
        $this->configurationFactory = $configurationFactory;
        $this->dispatcher = $dispatcher;
        $this->config = $this->configurationFactory->createConfiguration();
    }

    public function createLogger($channel)
    {
        $logger = $this->config->createLogger($channel);
        $event = new Event($logger);
        $this->dispatcher->dispatch('on_logger_create', $event);
        return $logger;
    }

}



