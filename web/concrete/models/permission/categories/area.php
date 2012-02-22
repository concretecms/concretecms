<?
defined('C5_EXECUTE') or die("Access Denied.");
class AreaPermissionKey extends PermissionKey {
	
	protected $area;
	
	public function getAreaObject() {
		return $this->area;
	}

	public static function getByID($pkID, Area $area) {
		$pk = self::load($pkID);
		if ($pk->getPermissionKeyID() > 0) {
			$pk->area = $area;
			return $pk;
		}
	}
	
	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
 		$r = $db->Execute('select peID, pdID from AreaPermissionAssignments where cID = ? and arHandle = ? and accessType = ? and pkID = ?', array(
 			$this->area->getCollectionID(), $this->area->getAreaHandle(), $accessType, $this->getPermissionKeyID()
 		));
 		$list = array();
 		$class = str_replace('AreaPermissionKey', 'AreaPermissionAssignment', get_class($this));
 		if (!class_exists($class)) {
 			$class = 'AreaPermissionAssignment';
 		}
 		while ($row = $r->FetchRow()) {
 			$ppa = new $class();
 			$ppa->setAccessType($accessType);
 			$ppa->loadPermissionDurationObject($row['pdID']);
 			$ppa->loadAccessEntityObject($row['peID']);
			$ppa->setAreaObject($area);
			$list[] = $ppa;
 		}
 		
 		return $list;
	}
	
	public function addAssignment(PermissionAccessEntity $pae, $durationObject = false, $accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$pdID = 0;
		if ($durationObject instanceof PermissionDuration) {
			$pdID = $durationObject->getPermissionDurationID();
		}
		
		$db->Replace('AreaPermissionAssignments', array(
			'cID' => $this->area->getCollectionID(),
			'arHandle' => $this->area->getAreaHandle(),
			'pkID' => $this->getPermissionKeyID(), 
			'peID' => $pae->getAccessEntityID(),
			'pdID' => $pdID,
			'accessType' => $accessType
		), array('cID', 'arHandle', 'peID', 'pkID'), true);
	}
	
	public function removeAssignment(PermissionAccessEntity $pe) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionAssignments where cID = ? and arHandle = ? and peID = ?', array($this->area->getCollectionID(), $this->area->getAreaHandle(), $pe->getAccessEntityID()));
		
	}
	
	public function getPermissionKeyToolsURL($task = false) {
		$area = $this->getAreaObject();
		$c = $area->getAreaCollectionObject();
		return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&arHandle=' . $area->getAreaHandle();
	}

}

class AreaPermissionAssignment extends PermissionAssignment {

	public function setAreaObject($area) {
		$this->area = $area;
	}


}