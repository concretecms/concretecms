<?php
namespace Concrete\Core\User\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\User\UserInfo as ConcreteUserInfo;

class UserInfo extends AbstractEvent {

	protected $ui;

	public function __construct(ConcreteUserInfo $ui) {
		$this->ui = $ui;
	}

	public function getUserInfoObject() {
		return $this->ui;
	}

}
