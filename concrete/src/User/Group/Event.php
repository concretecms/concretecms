<?php
namespace Concrete\Core\User\Group;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;

class Event extends AbstractEvent {

	protected $g;

	public function __construct(Group $g) {
		$this->g = $g;
	}

	public function getGroupObject() {
		return $this->g;
	}

}
