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

    public function __construct(ConfigurationFactory $configurationFactory, EventDispatcher $dispatcher)
    {
        $this->configurationFactory = $configurationFactory;
        $this->dispatcher = $dispatcher;
    }

    public function createLogger($channel)
    {
        /**
         * @var $config ConfigurationInterface
         */
        $config = $this->configurationFactory->createConfiguration();
        $logger = $config->createLogger($channel);
        $event = new Event($logger);
        $this->dispatcher->dispatch('on_logger_create', $event);
        return $logger;
    }

}



