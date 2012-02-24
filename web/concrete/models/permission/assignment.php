<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionAssignment extends Object {
	
	public function setAccessType($accessType) {
		$this->accessType = $accessType;
	}
	
	public function getAccessType() {
		return $this->accessType;
	}
	
	public function loadPermissionDurationObject($pdID) {
		$pd = PermissionDuration::getByID($pdID);
		$this->duration = $pd;
	}

	public function loadAccessEntityObject($peID) {
		$pe = PermissionAccessEntity::getByID($peID);
		$this->accessEntity = $pe;
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