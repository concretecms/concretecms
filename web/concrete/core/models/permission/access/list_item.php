<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PermissionAccessListItem extends Object {

	public function getPermissionAccessID() {return $this->paID;}
	public function setPermissionAccessID($paID) {$this->paID = $paID;}

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
	

	
}