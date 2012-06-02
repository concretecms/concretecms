<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionTarget {
	
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
		$akc = PermissionKeyCategory::getByID($this->pk->getPermissionKeyCategoryID());
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

	public function getPermissionAccessID() {
		$db = Loader::db();
		$paID = $db->GetOne('select paID from PermissionAssignments where pkID = ?', array($this->pk->getPermissionKeyID()));
		return $paID;
	}


}