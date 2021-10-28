<?php
namespace Concrete\Core\Logging;

use Symfony\Component\EventDispatcher\GenericEvent as AbstractEvent;
use Monolog\Logger as MonologLogger;

class Event extends AbstractEvent
{
    protected $logger;

    public function __construct(MonologLogger $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}
