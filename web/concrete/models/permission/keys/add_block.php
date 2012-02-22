<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddBlockAreaPermissionKey extends AreaPermissionKey  {

	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionBlockTypeAssignments where cID = ? and arHandle = ?', array($this->area->getCollectionID(), $this->area->getAreaHandle()));
		$db->Execute('delete from AreaPermissionBlockTypeAssignmentsCustom where cID = ? and arHandle = ?', array($this->area->getCollectionID(), $this->area->getAreaHandle()));
		if (is_array($args['blockTypesIncluded'])) { 
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->area->getCollectionID(), $this->area->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['blockTypesExcluded'])) { 
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->area->getCollectionID(), $this->area->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) { 
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->area->getCollectionID(), $this->area->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) { 
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->area->getCollectionID(), $this->area->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}

	}


	public function getAssignmentList($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionsObject instanceof Page && $accessType == AreaPermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 1;
			} else { 
				$permission = $db->GetOne('select permission from AreaPermissionBlockTypeAssignments where peID = ?', array($pe->getAccessEntityID()));
			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				$ctIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAssignmentsCustom where peID = ?', array($pe->getAccessEntityID()));
				$l->setBlockTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
	
}

class AddBlockAreaPermissionAssignment extends AreaPermissionAssignment {
	
	protected $customBlockTypeArray = array();
	protected $blockTypesAllowedPermission = 0;

	public function setBlockTypesAllowedPermission($permission) {
		$this->blockTypesAllowedPermission = $permission;
	}
	public function getBlockTypesAllowedPermission() {
		return $this->blockTypesAllowedPermission;
	}
	public function setBlockTypesAllowedArray($ctIDs) {
		$this->customBlockTypeArray = $ctIDs;
	}
	public function getBlockTypesAllowedArray() {
		return $this->customBlockTypeArray;
	}
	
	
}