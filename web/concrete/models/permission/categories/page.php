<?
defined('C5_EXECUTE') or die("Access Denied.");
class PagePermissionKey extends PermissionKey {
	
	public static function getByID($pkID) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			return $pk;
		}
	}

	public static function getByHandle($pkHandle) {
		$db = Loader::db();
		$pkID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($pkHandle));
		if ($pkID) { 
			$pk = self::load($pkID);
			if ($pk->getPermissionKeyID() > 0) {
				return $pk;
			}
		}
	}
	
	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$filterString = $this->buildAssignmentFilterString($accessType, $filterEntities);
 		$r = $db->Execute('select peID, pdID, accessType from PagePermissionAssignments where cID = ? and pkID = ? ' . $filterString, array(
 			$this->permissionObject->getPermissionsCollectionID(), $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('PagePermissionKey', 'PagePermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'PagePermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($row['accessType']);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setPermissionObject($page);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('PagePermissionAssignments', array(
			'cID' => $this->permissionObject->getCollectionID(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'peID', 'pkID'), false);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionAssignments where cID = ? and peID = ? and pkID = ?', array($this->permissionObject->getCollectionID(), $pe->getAccessEntityID(), $this->getPermissionKeyID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $this->getPermissionObject()->getCollectionID();
	}

}

class PagePermissionAssignment extends PermissionAssignment {


}