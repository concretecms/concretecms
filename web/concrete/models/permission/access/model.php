<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionAccess extends Object {
	
	public function getAccessList($paID) {
		$db = Loader::db();
		$class = get_called_class();
		$list = array();
		$r = $db->Execute('select peID, pdID, accessType from PermissionAccess where paID = ?', array($paID));
		while ($row = $r->FetchRow()) {
			$obj = new $class();
			$obj->setPropertiesFromArray($row);
			if ($row['pdID']) {
				$obj->loadPermissionDurationObject($row['pdID']);
			}
			if ($row['peID']) {
				$obj->loadAccessEntityObject($row['peID']);
			}
			$list[] = $obj;
		}
		return $list;	
	}
	
	public function setAccessType($accessType) {
		$this->accessType = $accessType;
	}
	
	public function getAccessType() {
		return $this->accessType;
	}
	
	public function loadPermissionDurationObject($pdID) {
		if ($pdID > 0) { 
			$pd = PermissionDuration::getByID($pdID);
			$this->duration = $pd;
		}
	}

	public function loadAccessEntityObject($peID) {
		if ($peID > 0) { 
			$pe = PermissionAccessEntity::getByID($peID);
			$this->accessEntity = $pe;
		}
	}
	
	public function getAccessEntityObject() {return $this->accessEntity;}
	public function getPermissionDurationObject() {return $this->duration;}
	
	public function setPermissionObject($object) {
		$this->permissionObject = $object;
	}
	public function getPermissionObject() {
		return $this->permissionObject;
	}
	
}