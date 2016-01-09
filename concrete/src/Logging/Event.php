<?php
namespace Concrete\Core\Logging;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;

class Event extends AbstractEvent {

	protected $logger;

	public function __construct(Logger $logger) {
		$this->logger = $logger;
	}

    public function getLogger() {
        return $this->logger;
    }
}
