<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddSubpagePagePermissionKey extends PagePermissionKey  {
	
	public function canAddExternalLink() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$canAddLinks = false;
		foreach($list as $l) {
			if (!$l->allowExternalLinks()) {
				$canAddLinks = false;
			} else {
				$canAddLinks = true;
			}
		}
		return $canAddLinks;
	}
	
	protected function getAllowedPageTypeIDs() {

		$u = new User();
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		
		$db = Loader::db();
		$allCTIDs = $db->GetCol('select ctID from PageTypes where ctIsInternal = 0');
		$ctIDs = array();
		foreach($list as $l) {
			if ($l->getPageTypesAllowedPermission() == 'N') {
				$ctIDs = array();
			}
			if ($l->getPageTypesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
					$ctIDs = array_values(array_diff($ctIDs, $l->getPageTypesAllowedArray()));
				} else { 
					$ctIDs = array_unique(array_merge($ctIDs, $l->getPageTypesAllowedArray()));
				}
			}
			if ($l->getPageTypesAllowedPermission() == 'A') {
				$ctIDs = $allCTIDs;
			}
		}
		
		return $ctIDs;
	}
	
	public function validate($ct = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedPageTypeIDs();
		if ($ct != false) {
			return in_array($ct->getCollectionTypeID(), $types);
		} else {
			return count($types) > 0;
		}
	}
	

	
}

class AddSubpagePagePermissionAccess extends PagePermissionAccess {

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
			$prow = $db->GetRow('select permission, externalLink from PagePermissionPageTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
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
				$ctIDs = $db->GetCol('select ctID from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
				$l->setPageTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
}

class AddSubpagePagePermissionAccessListItem extends PagePermissionAccessListItem {
	
	protected $customPageTypeArray = array();
	protected $pageTypesAllowedPermission = 'N';
	protected $allowExternalLinks = 0;

	public function setPageTypesAllowedPermission($permission) {
		$this->pageTypesAllowedPermission = $permission;
	}
	public function getPageTypesAllowedPermission() {
		return $this->pageTypesAllowedPermission;
	}
	public function setPageTypesAllowedArray($ctIDs) {
		$this->customPageTypeArray = $ctIDs;
	}
	public function getPageTypesAllowedArray() {
		return $this->customPageTypeArray;
	}
	
	public function setAllowExternalLinks($allow) {
		$this->allowExternalLinks = $allow;
	}
	
	public function allowExternalLinks() {
		return $this->allowExternalLinks;
	}
	
	
}