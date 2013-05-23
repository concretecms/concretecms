<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AggregatorPermissionAssignment extends PermissionAssignment {


	public function getPermissionAccessObject() {
		$db = Loader::db();
 		$r = $db->GetOne('select paID from AggregatorPermissionAssignments where agID = ? and pkID = ?', array(
 			$this->permissionObject->getAggregatorID(), $this->pk->getPermissionKeyID()
 		));
 		return PermissionAccess::getByID($r, $this->pk);
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update AggregatorPermissionAssignments set paID = 0 where pkID = ? and agID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getAggregatorID()));
	}
	
	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('AggregatorPermissionAssignments', array('agID' => $this->getPermissionObject()->getAggregatorID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('agID', 'pkID'), true);
		$pa->markAsInUse();
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&agID=' . $this->getPermissionObject()->getAggregatorID();
	}


}
