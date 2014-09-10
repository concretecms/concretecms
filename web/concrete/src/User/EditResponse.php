<?php
namespace Concrete\Core\User;
use UserInfo as ConcreteUserInfo;
use stdclass;

class EditResponse extends \Concrete\Core\Application\EditResponse {

	protected $users = array();

	public function setUser(ConcreteUserInfo $user) {
		$this->users[] = $user;
	}

	public function setUsers($users) {
		$this->users = $users;
	}

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		foreach($this->users as $user) {
			$uo = new stdClass;
			$uo->uID = $user->getUserID();
			$uo->displayName = $user->getUserDisplayName();
			$o->users[] = $uo;
		}
		return $o;
	}


}
