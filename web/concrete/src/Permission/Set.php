<?php
namespace Concrete\Core\Permission;
use Session;
class Set {

	protected $permissions;
	protected $pkCategoryHandle;

	public function addPermissionAssignment($pkID, $paID) {
		$this->permissions[$pkID] = $paID;
	}

	public function getPermissionAssignments() {
		return $this->permissions;
	}

	public function setPermissionKeyCategory($pkCategoryHandle) {
		$this->pkCategoryHandle = $pkCategoryHandle;
	}

	public function getPermissionKeyCategory() {
		return $this->pkCategoryHandle;
	}

	public function saveToSession() {
		Session::set('savedPermissionSet', serialize($this));
	}

	public static function getSavedPermissionSetFromSession() {
		$obj = unserialize(Session::get('savedPermissionSet'));
		return $obj;
	}
}
