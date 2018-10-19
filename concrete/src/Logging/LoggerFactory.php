<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger as Monologger;

class LoggerFactory
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
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

    public function createLog($channel)
    {
        $configuration = $this->config->get('concrete.log.configurations');
        $logger = new Monologger($channel);
        if ($configuration['simple']['enabled']) {
            $logger->pushHandler($this->createStandardDatabaseHandler($configuration['simple']['level']));
        }
        return $logger;
    }

}



