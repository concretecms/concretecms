<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

abstract class SimpleConfiguration implements ConfigurationInterface
{

    /**
     * The logging level to care about for all core logs.
     * @var $level
     */
    protected $coreLevel;

    public function __construct($coreLevel = Logger::DEBUG)
    {
        $this->coreLevel = $coreLevel;
    }

    abstract protected function createHandler($level);

    public function createLogger($channel)
    {
        $logger = new Logger($channel);
        $level = $this->coreLevel;
        if (!in_array($channel, Channels::getCoreChannels())) {
            $level = Logger::DEBUG;
        }

        $handler = $this->createHandler($level);
        $logger->pushHandler($handler);
        return $logger;
    }


}



