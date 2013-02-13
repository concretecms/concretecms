<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PagePermissionAssignment extends PermissionAssignment {

	public function getPermissionAccessObject() {
		$pa = PermissionCache::getAccessObject($this->pk, $this->getPermissionObject());
		if ($pa === -1) {
			return false;
		}

		if (!is_object($pa)) {
			$db = Loader::db();
			$r = $db->GetOne('select paID from PagePermissionAssignments where cID = ? and pkID = ?', array($this->getPermissionObject()->getPermissionsCollectionID(), $this->pk->getPermissionKeyID()));
			if ($r) {
				$pa = PermissionAccess::getByID($r, $this->pk, false);
			}
			if (is_object($pa)) {
				PermissionCache::addAccessObject($this->pk, $this->getPermissionObject(), $pa);
			} else {
				PermissionCache::addAccessObject($this->pk, $this->getPermissionObject(), -1);
			}
		}
		return $pa;
	}

	public function clearPermissionAssignment() {
		$db = Loader::db();
		$db->Execute('update PagePermissionAssignments set paID = 0 where pkID = ? and cID = ?', array($this->pk->getPermissionKeyID(), $this->getPermissionObject()->getPermissionsCollectionID()));
		PermissionCache::clearAccessObject($this->pk, $this->getPermissionObject());
	}

	public function assignPermissionAccess(PermissionAccess $pa) {
		$db = Loader::db();
		$db->Replace('PagePermissionAssignments', array('cID' => $this->getPermissionObject()->getPermissionsCollectionID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cID', 'pkID'), true);
		$pa->markAsInUse();
		PermissionCache::clearAccessObject($this->pk, $this->getPermissionObject());
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