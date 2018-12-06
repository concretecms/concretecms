<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class SimpleConfiguration implements ConfigurationInterface
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

    private function createStandardDatabaseHandler($level)
    {
        $handler = new DatabaseHandler($level);
        // set a more basic formatter.
        $output = "%message%";
        $formatter = new LineFormatter($output, null, true);
        $handler->setFormatter($formatter);
        return $handler;
    }

    public function createLogger($channel)
    {
        $logger = new Logger($channel);
        if (!in_array($channel, Channels::getCoreChannels())) {
            // We create a logger with all channels enabled
            $logger->pushHandler($this->createStandardDatabaseHandler(Logger::DEBUG));
        } else {
            $logger->pushHandler($this->createStandardDatabaseHandler($this->coreLevel));
        }
        return $logger;
    }


}



