<?php
namespace Concrete\Core\Permission\Assignment;
use PermissionAccess;
use PermissionKeyCategory;
use Loader;
class TreeNodeAssignment extends Assignment {

	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&treeNodeID=' . $this->getPermissionObject()->getTreeNodeID();
	}

	protected function getPermissionKeyToolsBaseURL(PermissionKeyCategory $akc) {
		$uh = Loader::helper('concrete/urls');
		return $uh->getToolsURL('permissions/categories/tree/node', $akc->getPackageHandle());
	}

	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from TreeNodePermissionAssignments where treeNodeID = ? and pkID = ?', array(
 			$this->permissionObject->getTreeNodePermissionsNodeID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update TreeNodePermissionAssignments set paID = 0 where pkID = ? and treeNodeID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getTreeNodeID()));
	}

	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('TreeNodePermissionAssignments', array('treeNodeID' => $this->permissionObject->getTreeNodeID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('treeNodeID', 'pkID'), true);
		$pa->markAsInUse();
	}


}
