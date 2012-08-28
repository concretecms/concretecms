<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AddSubpagePagePermissionAccess extends PagePermissionAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from PagePermissionPageTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission'], $row['externalLink']);
			$db->Execute('insert into PagePermissionPageTypeAccessList (peID, paID, permission, externalLink) values (?, ?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from PagePermissionPageTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['ctID']);
			$db->Execute('insert into PagePermissionPageTypeAccessListCustom  (peID, paID, ctID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPageTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from PagePermissionPageTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['pageTypesIncluded'])) { 
			foreach($args['pageTypesIncluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksIncluded'][$peID])) {
					$ext = $args['allowExternalLinksIncluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['pageTypesExcluded'])) { 
			foreach($args['pageTypesExcluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksExcluded'][$peID])) {
					$ext = $args['allowExternalLinksExcluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['ctIDInclude'])) { 
			foreach($args['ctIDInclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ctID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ctIDExclude'])) { 
			foreach($args['ctIDExclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ctID) values (?, ?, ?)', $v);
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
			$prow = $db->GetRow('select permission, externalLink from PagePermissionPageTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			if (is_array($prow) && $prow['permission']) { 
				$l->setPageTypesAllowedPermission($prow['permission']);
				$l->setAllowExternalLinks($prow['externalLink']);
				$permission = $prow['permission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setPageTypesAllowedPermission('A');
				$l->setAllowExternalLinks(1);
			} else {
				$l->setPageTypesAllowedPermission('N');
				$l->setAllowExternalLinks(0);
			}
			if ($permission == 'C') { 
				$ctIDs = $db->GetCol('select ctID from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setPageTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
}