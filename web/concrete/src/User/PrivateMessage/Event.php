<?php
namespace Concrete\Core\User\PrivateMessage;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;

class Event extends AbstractEvent {

	protected $pm;

	public function __construct(PrivateMessage $pm) {
		$this->pm = $pm;
	}

	public function getPrivateMessageObject() {
		return $this->pm;
	}

}
