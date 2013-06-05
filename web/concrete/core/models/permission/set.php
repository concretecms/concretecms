<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionSet {

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
		$_SESSION['savedPermissionSet'] = serialize($this);
	}
	
	public static function getSavedPermissionSetFromSession() {
		$obj = unserialize($_SESSION['savedPermissionSet']);
		return $obj;
	}
}