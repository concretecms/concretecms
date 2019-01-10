<?php
namespace Concrete\Core\Permission;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Configuration\SimpleConfiguration;
use Concrete\Core\Logging\Entry\Permission\Assignment\Assignment;
use Concrete\Core\Logging\LoggerFactory;
use Monolog\Logger as Monolog;
class Logger
{
    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->loggerFactory = $loggerFactory;
    }

    public function shouldLogAssignment()
    {
        $configuration = $this->loggerFactory->getConfiguration();
        if ($configuration instanceof SimpleConfiguration) {
            $level = Monolog::toMonologLevel($configuration->getCoreLevel());
            if ($level <= Monolog::INFO) {
                // only log this if the level is set to info or lower.
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public function log(Assignment $assignment)
    {
        if ($this->shouldLogAssignment()) {
            $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_PERMISSIONS);
            $logger->info($assignment->getMessage(), $assignment->getContext());
        }
    }
}
