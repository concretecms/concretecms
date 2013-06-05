<?php

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PermissionAssignment {

	protected $pk; // permissionkey
	protected $permissionObject;
	
	public function setPermissionObject($po) {
		$this->permissionObject = $po;
	}
	
	public function getPermissionObject() {
		return $this->permissionObject;
	}
	
	public function setPermissionKeyObject($pk) {
		$this->pk = $pk;
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		if (!$task) {
			$task = 'save_permission';
		}
		$uh = Loader::helper('concrete/urls');
		$class = substr(get_class($this), 0, strrpos(get_class($this), 'PermissionAssignment'));
		$handle = Loader::helper('text')->uncamelcase($class);
		if ($handle) {
			$akc = PermissionKeyCategory::getByHandle($handle);
		} else {
			$akc = PermissionKeyCategory::getByID($this->pk->getPermissionKeyCategoryID());
		}
		$url = $uh->getToolsURL('permissions/categories/' . $akc->getPermissionKeyCategoryHandle(), $akc->getPackageHandle());
		$token = Loader::helper('validation/token')->getParameter($task);
		$url .= '?' . $token . '&task=' . $task . '&pkID=' . $this->pk->getPermissionKeyID();
		return $url;
	}
	
	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PermissionAssignments set paID = 0 where pkID = ?', array($this->pk->getPermissionKeyID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PermissionAssignments', array('paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('pkID'), true);
		$pa->markAsInUse();
	}

	public function getPermissionAccessObject() {
		$db = Loader::db();
		$paID = $db->GetOne('select paID from PermissionAssignments where pkID = ?', array($this->pk->getPermissionKeyID()));
		return PermissionAccess::getByID($paID, $this->pk);
	}
	
}