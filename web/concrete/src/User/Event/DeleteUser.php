<?php
namespace Concrete\Core\User\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\User\User as ConcreteUserInfo;

class DeleteUser extends UserInfo {

	protected $proceed = true;

	public function cancelDelete() {
		$this->proceed = false;
	}

	public function proceed() {
		return $this->proceed;
	}

}
