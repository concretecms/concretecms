<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_TreeNodePermissionKey extends PermissionKey {

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