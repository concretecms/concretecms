<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddBlockToAreaAreaPermissionKey extends AreaPermissionKey  {

	public function copyFromPageToArea() {
		$db = Loader::db();
		$inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array('add_block'));
		$r = $db->Execute('select peID, pa.paID from PermissionAssignments pa inner join PermissionAccessList pal on pa.paID = pal.paID where pkID = ?', array(
			$inheritedPKID
		));
		if ($r) { 
			while ($row = $r->FetchRow()) {
				$db->Replace('AreaPermissionAssignments', array(
					'cID' => $this->permissionObject->getCollectionID(), 
					'arHandle' => $this->permissionObject->getAreaHandle(), 
					'pkID' => $this->getPermissionKeyID(),
					'paID' => $row['paID']
				), array('cID', 'arHandle', 'pkID'), true);
					
				$rx = $db->Execute('select permission from BlockTypePermissionBlockTypeAccessList where paID = ? and peID = ?', array(
						$row['paID'], $row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAccessList', array(
						'peID' => $row['peID'],
						'permission' => $rowx['permission'],
						'paID' => $row['paID']
					), array('paID', 'peID'), true);				
				}
				$db->Execute('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', array(
					$row['paID']
				));
				$rx = $db->Execute('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ? and peID = ?', array(
						$row['paID'], $row['peID']
					));
				while ($rowx = $rx->FetchRow()) {
					$db->Replace('AreaPermissionBlockTypeAccessListCustom', array(
						'paID' => $row['paID'],
						'btID' => $rowx['btID'],
						'peID' => $row['peID']
					), array('paID', 'peID', 'btID'), true);				
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

	
}

class AddBlockToAreaAreaPermissionAccess extends AreaPermissionAccess {

	public function duplicate() {
		$newPA = parent::duplicate();
		$db = Loader::db();
		$r = $db->Execute('select * from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into AreaPermissionBlockTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['btID']);
			$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom  (peID, paID, btID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function getAccessListItems($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		$pobj = $this->getPermissionObjectToCheck();
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($pobj instanceof Page) {
				$permission = $db->GetOne('select permission from BlockTypePermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
			} else { 
				$permission = $db->GetOne('select permission from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
			}
			if ($permission != 'N' && $permission != 'C') {
				$permission = 'A';
			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') { 
				if ($pobj instanceof Area) { 
					$btIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
				} else { 
					$btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
				}
				$l->setBlockTypesAllowedArray($btIDs);
			}
		}
		return $list;
	}

	public function save($args) {
		$db = Loader::db();
		parent::save();
		$db->Execute('delete from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['blockTypesIncluded'])) { 
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['blockTypesExcluded'])) { 
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) { 
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) { 
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) { 
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}
	}

}

class AddBlockToAreaAreaPermissionAccessListItem extends AreaPermissionAccessListItem {
	
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