<?
defined('C5_EXECUTE') or die("Access Denied.");

class EditPageThemePagePermissionKey extends PagePermissionKey  {
	
	protected function getAllowedThemeIDs() {

		$u = new User();
		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return array();
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
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
	
	
}

class EditPageThemePagePermissionAccess extends PagePermissionAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from PagePermissionThemeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into PagePermissionThemeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from PagePermissionThemeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['ptID']);
			$db->Execute('insert into PagePermissionThemeAccessListCustom  (peID, paID, ptID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from PagePermissionThemeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from PagePermissionThemeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['themesIncluded'])) { 
			foreach($args['themesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['themesExcluded'])) { 
			foreach($args['themesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['ptIDInclude'])) { 
			foreach($args['ptIDInclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionThemeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ptIDExclude'])) { 
			foreach($args['ptIDExclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionThemeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

	}


	public function getAccessListItems($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$prow = $db->GetRow('select permission from PagePermissionThemeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			if (is_array($prow) && $prow['permission']) { 
				$l->setThemesAllowedPermission($prow['permission']);
				$permission = $prow['permission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setThemesAllowedPermission('A');
			} else {
				$l->setThemesAllowedPermission('N');
			}
			if ($permission == 'C') { 
				$ptIDs = $db->GetCol('select ptID from PagePermissionThemeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setThemesAllowedArray($ptIDs);
			}
		}
		return $list;
	}

}

class EditPageThemePagePermissionAccessListItem extends PagePermissionAccessListItem {
	
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