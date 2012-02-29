<?
defined('C5_EXECUTE') or die("Access Denied.");

class AddSubpagePagePermissionKey extends PagePermissionKey  {
	
	public function canAddExternalLink() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$canAddLinks = false;
		foreach($list as $l) {
			if ($l->allowExternalLinks() == '0') {
				$canAddLinks = false;
			}
			if ($l->allowExternalLinks() == '1') {
				$canAddLinks = true;
			}
		}
		return $canAddLinks;
	}
	
	public function validate($ct = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		// these are assignments that apply to me
		$canAddSubpage = false;
		foreach($list as $l) {
			if ($l->getPageTypesAllowedPermission() == '0') {
				$canAddSubpage = false;
			}
			if ($l->getPageTypesAllowedPermission() == 'C') {
				if (is_object($ct)) { 
					if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
						$canAddSubpage = !in_array($ct->getCollectionTypeID(), $l->getPageTypesAllowedArray());
					} else { 
						$canAddSubpage = in_array($ct->getCollectionTypeID(), $l->getPageTypesAllowedArray());
					}
				} else {
					$canAddSubpage = true;
				}
			}
			if ($l->getPageTypesAllowedPermission() == '1') {
				$canAddSubpage = true;
			}
		}
		
		return $canAddSubpage;
	}
	
	public function savePermissionKey($args) {
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPageTypeAssignments where cID = ?', array($this->permissionObject->getCollectionID()));
		$db->Execute('delete from PagePermissionPageTypeAssignmentsCustom where cID = ?', array($this->permissionObject->getCollectionID()));
		if (is_array($args['pageTypesIncluded'])) { 
			foreach($args['pageTypesIncluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksIncluded'][$peID])) {
					$ext = $args['allowExternalLinksIncluded'][$peID];
				}
				$v = array($this->permissionObject->getCollectionID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAssignments (cID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['pageTypesExcluded'])) { 
			foreach($args['pageTypesExcluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksExcluded'][$peID])) {
					$ext = $args['allowExternalLinksExcluded'][$peID];
				}
				$v = array($this->permissionObject->getCollectionID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAssignments (cID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['ctIDInclude'])) { 
			foreach($args['ctIDInclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->permissionObject->getCollectionID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAssignmentsCustom (cID, peID, ctID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ctIDExclude'])) { 
			foreach($args['ctIDExclude'] as $peID => $ctIDs) {
				foreach($ctIDs as $ctID) { 
					$v = array($this->permissionObject->getCollectionID(), $peID, $ctID);
					$db->Execute('insert into PagePermissionPageTypeAssignmentsCustom (cID, peID, ctID) values (?, ?, ?)', $v);
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
			$prow = $db->GetRow('select permission, externalLink from PagePermissionPageTypeAssignments where peID = ?', array($pe->getAccessEntityID()));
			if (is_array($prow) && isset($prow['permission'])) { 
				$l->setPageTypesAllowedPermission($prow['permission']);
				$l->setAllowExternalLinks($prow['externalLink']);
				$permission = $prow['permission'];
			} else {
				$l->setPageTypesAllowedPermission(1);
				$l->setAllowExternalLinks(1);
			}
			if ($permission == 'C') { 
				$ctIDs = $db->GetCol('select ctID from PagePermissionPageTypeAssignmentsCustom where peID = ?', array($pe->getAccessEntityID()));
				$l->setPageTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
	
}

class AddSubpagePagePermissionAssignment extends PagePermissionAssignment {
	
	protected $customPageTypeArray = array();
	protected $pageTypesAllowedPermission = 0;
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