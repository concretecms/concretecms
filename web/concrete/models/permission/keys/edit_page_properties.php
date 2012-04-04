<?
defined('C5_EXECUTE') or die("Access Denied.");

class EditPagePropertiesPermissionKey extends PagePermissionKey  {
	
	/*
	public function canAddExternalLink() {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAssignmentList(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
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
		$list = $this->getAssignmentList(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
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
			$prow = $db->GetRow('select permission, externalLink from PagePermissionPageTypeAssignments where peID = ? and cID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getPermissionsCollectionID()));
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
				$ctIDs = $db->GetCol('select ctID from PagePermissionPageTypeAssignmentsCustom where peID = ? and cID = ?', array($pe->getAccessEntityID(), $this->permissionObject->getPermissionsCollectionID()));
				$l->setPageTypesAllowedArray($ctIDs);
			}
		}
		return $list;
	}
	*/
	
}

class EditPagePropertiesPermissionAssignment extends PagePermissionAssignment {
	
	protected $customAttributeKeyArray = array();
	protected $attributesAllowedPermission = 'N';
	protected $allowEditName = 0;
	protected $allowEditDateTime = 0;
	protected $allowEditUID = 0;
	protected $allowEditDescription = 0;
	protected $allowEditPagePaths = 0;

	public function setAttributesAllowedPermission($permission) {
		$this->attributesAllowedPermission = $permission;
	}
	public function getAttributesAllowedPermission() {
		return $this->attributesAllowedPermission;
	}
	public function setAttributesAllowedArray($ctIDs) {
		$this->customAttributeKeyArray = $ctIDs;
	}
	public function getAttributesAllowedArray() {
		return $this->customAttributeKeyArray;
	}
	
	public function setAllowEditName($allow) {
		$this->allowEditName = $allow;
	}
	
	public function allowEditPageName() {
		return $this->allowEditName;
	}

	public function setAllowEditDateTime($allow) {
		$this->allowEditDateTime = $allow;
	}
	
	public function allowEditDateTime() {
		return $this->allowEditDateTime;
	}

	public function setAllowEditUserID($allow) {
		$this->allowEditUID = $allow;
	}
	
	public function allowEditUserID() {
		return $this->allowEditUID;
	}

	public function setAllowEditDescription($allow) {
		$this->allowEditDescription = $allow;
	}
	
	public function allowEditDescription() {
		return $this->allowEditDescription;
	}

	public function setAllowEditEditPaths($allow) {
		$this->allowEditPagePaths = $allow;
	}
	
	public function allowEditPagePaths() {
		return $this->allowEditPagePaths;
	}
	
	
}