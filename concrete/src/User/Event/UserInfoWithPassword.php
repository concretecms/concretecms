<?php
namespace Concrete\Core\User\Event;
use \Symfony\Component\EventDispatcher\Event as AbstractEvent;
use \Concrete\Core\User\User as ConcreteUserInfo;

class UserInfoWithPassword extends UserInfo {

	protected $uPassword;

	public function setUserPassword($uPassword) {
		$this->uPassword = $uPassword;
	}

	public function getUserPassword() {
		return $this->uPassword;
	}

}
