<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger as Monologger;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoggerFactory
{

    const CHANNEL_APPLICATION = 'application';

    /**
     * The logger object used with facades. Not really the best way of logging but it can be
     * convenient.
     * @var \Monolog\Logger
     */
    protected $applicationLogger;

    /**
     * Handlers to attach to loggers created by thislogger factory. If this is populated it will be used for all
     * loggers created by this factory. It overrides the custom event-based handlers, as well as the standard
     * concrete.log.configurations setup
     * @var HandlerInterface[]
     */
    protected $loggerHandlers = [];

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(Repository $config, EventDispatcher $dispatcher)
    {
        $this->config = $config;
        $this->dispatcher = $dispatcher;
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

    public function setLoggerHandlers($handlers)
    {
        $this->loggerHandlers = $handlers;
    }

    public function createLogger($channel)
    {
        $handlers = [];
        $logger = new Monologger($channel);
        if (count($this->loggerHandlers)) {
            $handlers = $this->loggerHandlers;
        } else {
            $configuration = $this->config->get('concrete.log.configurations');
            if ($configuration['simple']['enabled']) {
                $handlers[] = $this->createStandardDatabaseHandler($configuration['simple']['level']);
            }
        }
        foreach($handlers as $handler) {
            $logger->pushHandler($handler);
        }

        $event = new Event($logger);
        $this->dispatcher->dispatch('on_logger_create', $event);
        return $logger;
    }

    public function getApplicationLogger()
    {
        if (!isset($this->applicationLogger)) {
            $this->applicationLogger = $this->createLogger(self::CHANNEL_APPLICATION);
        }
        return $this->applicationLogger;
    }

    /**
     * Passes any method called against the factory facade into the default application channel.
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $logger = $this->getApplicationLogger();
        call_user_func_array([$logger, $name], $arguments);
    }

}



