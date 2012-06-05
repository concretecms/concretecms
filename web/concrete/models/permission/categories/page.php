<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {

	protected $multiplePageArray; // bulk operations
	public function setMultiplePageArray($pages) {
		$this->multiplePageArray = $pages;
	}
	public function getMultiplePageArray() {
		return $this->multiplePageArray;
	}
	

}

class PagePermissionAssignment extends PermissionAssignment {

	public function getPermissionAccessObject() {
		$db = Loader::db();
		$paID = $db->GetOne('select paID from PagePermissionAssignments where cID = ? and pkID = ?', array($this->getPermissionObject()->getPermissionsCollectionID(), $this->pk->getPermissionKeyID()));
		$pae = PermissionAccess::getByID($paID, $this->pk);
		
		$c = $this->getPermissionObject();
		$workflows = PageWorkflowProgress::getList($c);
		
		$accessObjects = array();
		if (count($workflows) > 0) {
			foreach($workflows as $wff) {
				$wf = $wff->getWorkflowObject();
				$pkx = clone $this->pk;
				$pax = $wf->getPermissionAccessObject($pkx, $wff);	
				if (is_object($pax)) {
					$accessObjects[] = $pax;
				}
			}
		}
		if (count($accessObjects) > 0) {
			if (is_object($pae)) {
				$accessObjects[] = $pae;
			}
			$pae = PermissionAccess::createByMerge($accessObjects);
		}
		return $pae;
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PagePermissionAssignments set paID = 0 where pkID = ? and cID = ?', array($this->pk->getPermissionKeyID(), $this->getPermissionObject()->getPermissionsCollectionID()));
	}

	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PagePermissionAssignments', array('cID' => $this->getPermissionObject()->getPermissionsCollectionID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cID', 'pkID'), true);
		$pa->markAsInUse();
	}
		
	public function getPermissionKeyToolsURL($task = false) {
		$pageArray = $this->pk->getMultiplePageArray();
		if (is_array($pageArray) && count($pageArray) > 0) {
			$cIDStr = '';
			foreach($pageArray as $sc) {
				$cIDStr .= '&cID[]=' . $sc->getCollectionID();
			}
			return parent::getPermissionKeyToolsURL($task) . $cIDStr;
		} else {
			return parent::getPermissionKeyToolsURL($task) . '&cID=' . $this->getPermissionObject()->getCollectionID();
		}
	}
	
	
}

class PagePermissionAccess extends PermissionAccess {


}

class PagePermissionAccessListItem extends PermissionAccessListItem {


}