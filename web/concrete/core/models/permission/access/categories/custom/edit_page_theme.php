<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_EditPageThemePagePermissionAccess extends PagePermissionAccess {

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