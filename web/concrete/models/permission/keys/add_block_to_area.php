<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddBlockToAreaAreaPermissionKey extends AreaPermissionKey  {

	public function copyFromPageToArea() {
		$db = Loader::db();
		$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array('add_block'));
		$r = $db->Execute('select peID, accessType from BlockTypePermissionAssignments where pkID = ?', array(
			$inheritedPKID
		));
		if ($r) { 
			while ($row = $r->FetchRow()) {
				$db->Replace('AreaPermissionAssignments', array(
					'cID' => $this->permissionObject->getCollectionID(), 
					'arHandle' => $this->permissionObject->getAreaHandle(), 
					'pkID' => $this->getPermissionKeyID(),
					'accessType' => $row['accessType'],
					'peID' => $row['peID']), array('cID', 'arHandle', 'peID', 'pkID'), true);
					
				$rx = $db->Execute('select permission from BlockTypePermissionBlockTypeAssignments where peID = ?', array(
						$row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAssignments', array(
						'cID' => $this->permissionObject->getCollectionID(), 
						'arHandle' => $this->permissionObject->getAreaHandle(), 
						'permission' => $rowx['permission'],
						'peID' => $row['peID']
					), array('cID', 'arHandle', 'peID'), true);				
				}
				$rx = $db->Execute('select btID from BlockTypePermissionBlockTypeAssignmentsCustom where peID = ?', array(
						$row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAssignmentsCustom', array(
						'cID' => $this->permissionObject->getCollectionID(), 
						'arHandle' => $this->permissionObject->getAreaHandle(), 
						'btID' => $rowx['btID'],
						'peID' => $row['peID']
					), array('cID', 'arHandle', 'peID', 'btID'), true);				
				}
			}
		}
	}
	
	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from AreaPermissionBlockTypeAssignments where cID = ? and arHandle = ?', array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle()));
		$db->Execute('delete from AreaPermissionBlockTypeAssignmentsCustom where cID = ? and arHandle = ?', array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle()));
		if (is_array($args['blockTypesIncluded'])) { 
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['blockTypesExcluded'])) { 
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAssignments (cID, arHandle, peID, permission) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) { 
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) { 
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->permissionObject->getCollectionID(), $this->permissionObject->getAreaHandle(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAssignmentsCustom (cID, arHandle, peID, btID) values (?, ?, ?, ?)', $v);
				}
			}
		}
	}

	protected function getAllowedBlockTypeIDs() {

		$u = new User();
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(AreaPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		
		$db = Loader::db();
		$dsh = Loader::helper('concrete/dashboard');
		if ($dsh->inDashboard()) {
			$allBTIDs = $db->GetCol('select btID from BlockTypes');
		} else { 
			$allBTIDs = $db->GetCol('select btID from BlockTypes where btIsInternal = 0');
		}
		$btIDs = array();
		foreach($list as $l) {
			if ($l->getBlockTypesAllowedPermission() == 'N') {
				$btIDs = array();
			}
			if ($l->getBlockTypesAllowedPermission() == 'C') {
				if ($l->getAccessType() == AreaPermissionKey::ACCESS_TYPE_EXCLUDE) {
					$btIDs = array_values(array_diff($btIDs, $l->getBlockTypesAllowedArray()));
				} else { 
					$btIDs = array_unique(array_merge($btIDs, $l->getBlockTypesAllowedArray()));
				}
			}
			if ($l->getBlockTypesAllowedPermission() == 'A') {
				$btIDs = $allBTIDs;
			}
		}
		
		return $btIDs;
	}
	
	public function validate($bt = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedBlockTypeIDs();
		if ($bt != false) {
			return in_array($bt->getBlockTypeID(), $types);
		} else {
			return count($types) > 0;
		}
	}	

	public function getAssignmentList($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page) {
				$permission = $db->GetOne('select permission from BlockTypePermissionBlockTypeAssignments where peID = ?', array($pe->getAccessEntityID()));
			} else { 
				$permission = $db->GetOne('select permission from AreaPermissionBlockTypeAssignments where peID = ? and cID = ? and arHandle = ?', array($pe->getAccessEntityID(), $this->permissionObjectToCheck->getCollectionID(), $this->permissionObjectToCheck->getAreaHandle()));
			}
			if ($permission != 'N' && $permission != 'C') {
				$permission = 'A';
			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				if ($this->permissionObjectToCheck instanceof Area) { 
					$cID = $this->permissionObjectToCheck->getCollectionID();
					$arHandle = $this->permissionObjectToCheck->getAreaHandle();
					$btIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAssignmentsCustom where peID = ? and cID = ? and arHandle = ?', array($pe->getAccessEntityID(), $cID, $arHandle));
				} else { 
					$btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAssignmentsCustom where peID = ?', array($pe->getAccessEntityID()));
				}
				$l->setBlockTypesAllowedArray($btIDs);
			}
		}
		return $list;
	}
	
}

class AddBlockToAreaAreaPermissionAssignment extends AreaPermissionAssignment {
	
	protected $customBlockTypeArray = array();
	protected $blockTypesAllowedPermission = 'N';

	public function setBlockTypesAllowedPermission($permission) {
		$this->blockTypesAllowedPermission = $permission;
	}
	public function getBlockTypesAllowedPermission() {
		return $this->blockTypesAllowedPermission;
	}
	public function setBlockTypesAllowedArray($btIDs) {
		$this->customBlockTypeArray = $btIDs;
	}
	public function getBlockTypesAllowedArray() {
		return $this->customBlockTypeArray;
	}
	
	
}