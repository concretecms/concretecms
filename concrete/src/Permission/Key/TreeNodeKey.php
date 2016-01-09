<?php
namespace Concrete\Core\Permission\Key;
use Loader;
class TreeNodeKey extends Key {

	public function copyFromParentNodeToCurrentNode() {
		$db = Loader::db();
		$paID = $this->getPermissionAccessID();
		if ($paID) {
			$db = Loader::db();
			$db->Replace('TreeNodePermissionAssignments', array(
				'treeNodeID' => $this->permissionObject->getTreeNodeID(),
				'paID' => $paID,
				'pkID' => $this->getPermissionKeyID()
			),
			array('treeNodeID', 'pkID'), true);
		}
	}


}
