<?
defined('C5_EXECUTE') or die("Access Denied.");

class EditPageThemePagePermissionKey extends PagePermissionKey  {
	
	protected function getAllowedThemeIDs() {

		$u = new User();
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		
		$db = Loader::db();
		$allptIDs = $db->GetCol('select ptID from PageThemes order by ptID asc');
		$ptIDs = array();
		foreach($list as $l) {
			if ($l->getThemesAllowedPermission() == 'N') {
				$ptIDs = array();
			}
			if ($l->getThemesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
					$ptIDs = array_values(array_diff($ptIDs, $l->getThemesAllowedArray()));
				} else { 
					$ptIDs = array_unique(array_merge($ptIDs, $l->getThemesAllowedArray()));
				}
			}
			if ($l->getThemesAllowedPermission() == 'A') {
				$ptIDs = $allptIDs;
			}
		}
		
		return $ptIDs;
	}
	
	public function validate($theme = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$themes = $this->getAllowedThemeIDs();
		if ($theme != false) {
			return in_array($theme->getThemeID(), $themes);
		} else {
			return count($themes) > 0;
		}
	}
	
	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionThemeAssignments where cID = ?', array($this->permissionObject->getCollectionID()));
		$db->Execute('delete from PagePermissionThemeAssignmentsCustom where cID = ?', array($this->permissionObject->getCollectionID()));
		if (is_array($args['themesIncluded'])) { 
			foreach($args['themesIncluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAssignments (cID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['themesExcluded'])) { 
			foreach($args['themesExcluded'] as $peID => $permission) {
				$v = array($this->permissionObject->getCollectionID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAssignments (cID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['ptIDInclude'])) { 
			foreach($args['ptIDInclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->permissionObject->getCollectionID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionThemeAssignmentsCustom (cID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ptIDExclude'])) { 
			foreach($args['ptIDExclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->permissionObject->getCollectionID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionThemeAssignmentsCustom (cID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

	}


	public function getAssignmentList($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAssignmentList($accessType, $filterEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$prow = $db->GetRow('select permission from PagePermissionThemeAssignments where peID = ? and cID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getPermissionsCollectionID()));
			if (is_array($prow) && $prow['permission']) { 
				$l->setThemesAllowedPermission($prow['permission']);
				$permission = $prow['permission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setThemesAllowedPermission('A');
			} else {
				$l->setThemesAllowedPermission('N');
			}
			if ($permission == 'C') { 
				$ptIDs = $db->GetCol('select ptID from PagePermissionThemeAssignmentsCustom where peID = ? and cID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getPermissionsCollectionID()));
				$l->setThemesAllowedArray($ptIDs);
			}
		}
		return $list;
	}
	
}

class EditPageThemePagePermissionAssignment extends PagePermissionAssignment {
	
	protected $customThemeArray = array();
	protected $themesAllowedPermission = 'N';

	public function setThemesAllowedPermission($permission) {
		$this->themesAllowedPermission = $permission;
	}
	public function getThemesAllowedPermission() {
		return $this->themesAllowedPermission;
	}
	public function setThemesAllowedArray($ptIDs) {
		$this->customThemeArray = $ptIDs;
	}
	public function getThemesAllowedArray() {
		return $this->customThemeArray;
	}
	
}