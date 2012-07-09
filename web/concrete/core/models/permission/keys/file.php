<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_FilePermissionKey extends PermissionKey {


	public function validate() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return false;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$valid = false;
		$list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$valid = true;
			}
			if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
				$valid = false;
			}
		}
		return $valid;		
	}
	
	
	public function copyFromFileSetToFile() {
		$db = Loader::db();
		$paID = $this->getPermissionAccessID();
		if (is_array($paID)) {
			// we have to merge the permissions access object into a new one.
			$pa = PermissionAccess::create($this);
			foreach($paID as $paID) {
				$pax = PermissionAccess::getByID($paID, $this);
				$pax->duplicate($pa);
			}
			$paID = $pa->getPermissionAccessID();
		}
		if ($paID) {
			$db = Loader::db();
			$db->Replace('FilePermissionAssignments', array(
				'fID' => $this->permissionObject->getFileID(), 
				'pkID' => $this->getPermissionKeyID(),
				'paID' => $paID), array('fID', 'paID', 'pkID'), true);				

		}
	}



}