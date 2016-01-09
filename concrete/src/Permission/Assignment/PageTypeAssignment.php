<?php
namespace Concrete\Core\Permission\Assignment;
use PermissionAccess;
use Loader;
class PageTypeAssignment extends Assignment {

	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from PageTypePermissionAssignments where ptID = ? and pkID = ?', array(
 			$this->permissionObject->getPageTypeID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PageTypePermissionAssignments set paID = 0 where pkID = ? and ptID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getPageTypeID()));
	}

	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PageTypePermissionAssignments', array('ptID' => $this->getPermissionObject()->getPageTypeID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('ptID', 'pkID'), true);
		$pa->markAsInUse();
	}

	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&ptID=' . $this->getPermissionObject()->getPageTypeID();
	}


}
