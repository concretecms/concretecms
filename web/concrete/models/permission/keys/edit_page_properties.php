<?
defined('C5_EXECUTE') or die("Access Denied.");

class EditPagePropertiesPagePermissionKey extends PagePermissionKey  {


	public function getMyAccessList() {
		$u = new User();
		$asl = new EditPagePropertiesPagePermissionAccessListItem();
		
		$db = Loader::db();
		$allAKIDs = $db->GetCol('select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akCategoryHandle = \'collection\'');

		if ($u->isSuperUser()) {
			$asl->setAllowEditName(1);
			$asl->setAllowEditDateTime(1);
			$asl->setAllowEditUserID(1);
			$asl->setAllowEditDescription(1);
			$asl->setAllowEditPaths(1);
			$asl->setAttributesAllowedArray($allAKIDs);
			$asl->setAttributesAllowedPermission('A');
			return $asl;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$list = $this->getAccessListItems(PagePermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		$properties = array();
		
		$excluded = array();
		$akIDs = array();
		$u = new User();
		foreach($list as $l) {

			if ($l->allowEditName() && (!in_array('name', $excluded))) {
				$asl->setAllowEditName(1);
			}
			if ($l->allowEditDateTime() && (!in_array('date', $excluded))) {
				$asl->setAllowEditDateTime(1);
			}
			if ($l->allowEditUserID() && (!in_array('uID', $excluded))) {
				$asl->setAllowEditUserID(1);
			}
			if ($l->allowEditDescription() && (!in_array('description', $excluded))) {
				$asl->setAllowEditDescription(1);
			}
			if ($l->allowEditPaths() && (!in_array('paths', $excluded))) {
				$asl->setAllowEditPaths(1);
			}		
			
			if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE && $l->allowEditName()) {
				$asl->setAllowEditName(0);
				$excluded[] = 'name';
			}
			if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE && $l->allowEditDateTime()) {
				$asl->setAllowEditDateTime(0);
				$excluded[] = 'date';
			}
			if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE && $l->allowEditUserID()) {
				$asl->setAllowEditUserID(0);
				$excluded[] = 'uID';
			}
			if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE && $l->allowEditDescription()) {
				$asl->setAllowEditDescription(0);
				$excluded[] = 'description';
			}
			if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE && $l->allowEditPaths()) {
				$asl->setAllowEditPaths(0);
				$excluded[] = 'paths';
			}

			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
				if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_EXCLUDE) {
					$akIDs = array_values(array_diff($akIDs, $l->getAttributesAllowedArray()));
				} else { 
					$akIDs = array_unique(array_merge($akIDs, $l->getAttributesAllowedArray()));
				}
			}

			if ($l->getAttributesAllowedPermission() == 'A') {
				$akIDs = $allAKIDs;
				$asl->setAttributesAllowedPermission('A');
			}
		}	
		
		$asl->setAttributesAllowedArray($akIDs);
		return $asl;
	}


	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		$asl = $this->getMyAccessList();
		if (is_object($obj)) {
			if ($obj instanceof CollectionAttributeKey) {
				if ($asl->getAttributesAllowedPermission() == 'A') {
					return true;
				}
				if ($asl->getAttributesAllowedPermission() == 'C' && in_array($obj->getAttributeKeyID(), $asl->getAttributesAllowedArray())) {
					return true;
				} else {
					return false;
				}				
			}
		}

		if (
			$asl->allowEditName() || 
			$asl->allowEditDescription() || 
			$asl->allowEditDateTime() || 
			$asl->allowEditUserID() || 
			$asl->allowEditPaths() || 
			($asl->getAttributesAllowedPermission() == 'A' || ($asl->getAttributesAllowedPermission() == 'C' && count($asl->getAttributesAllowedArray() > 0)))) {
				return true;
		} else {
			return false;
		}
	}
	
	
}

class EditPagePropertiesPagePermissionAccess extends PagePermissionAccess {

	public function duplicate() {
		$newPA = parent::duplicate();
		$db = Loader::db();
		$r = $db->Execute('select * from PagePermissionPropertyAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), 
			$row['attributePermission'],
			$row['name'],
			$row['publicDateTime'],
			$row['uID'],
			$row['description'],
			$row['paths']
			);
			$db->Execute('insert into PagePermissionPropertyAccessList (peID, paID, attributePermission, name, publicDateTime, uID, description, paths) values (?, ?, ?, ?, ?, ?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from PagePermissionPropertyAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['akID']);
			$db->Execute('insert into PagePermissionPropertyAttributeAccessListCustom  (peID, paID, akID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPropertyAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from PagePermissionPropertyAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['propertiesIncluded'])) { 
			foreach($args['propertiesIncluded'] as $peID => $attributePermission) {
				$allowEditName = 0;
				$allowEditDateTime = 0;
				$allowEditUID = 0;
				$allowEditDescription = 0;
				$allowEditPaths = 0;
				if (!empty($args['allowEditName'][$peID])) {
					$allowEditName = $args['allowEditName'][$peID];
				}
				if (!empty($args['allowEditDateTime'][$peID])) {
					$allowEditDateTime = $args['allowEditDateTime'][$peID];
				}
				if (!empty($args['allowEditUID'][$peID])) {
					$allowEditUID = $args['allowEditUID'][$peID];
				}
				if (!empty($args['allowEditDescription'][$peID])) {
					$allowEditDescription = $args['allowEditDescription'][$peID];
				}
				if (!empty($args['allowEditPaths'][$peID])) {
					$allowEditPaths = $args['allowEditPaths'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $attributePermission, $allowEditName, $allowEditDateTime, $allowEditUID, $allowEditDescription, $allowEditPaths);
				$db->Execute('insert into PagePermissionPropertyAccessList (paID, peID, attributePermission, name, publicDateTime, uID, description, paths) values (?, ?, ?, ?, ?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['propertiesExcluded'])) { 
			foreach($args['propertiesExcluded'] as $peID => $attributePermission) {
				$allowEditNameExcluded = 0;
				$allowEditDateTimeExcluded = 0;
				$allowEditUIDExcluded = 0;
				$allowEditDescriptionExcluded = 0;
				$allowEditPathsExcluded = 0;
				if (!empty($args['allowEditNameExcluded'][$peID])) {
					$allowEditNameExcluded = $args['allowEditNameExcluded'][$peID];
				}
				if (!empty($args['allowEditDateTimeExcluded'][$peID])) {
					$allowEditDateTimeExcluded = $args['allowEditDateTimeExcluded'][$peID];
				}
				if (!empty($args['allowEditUIDExcluded'][$peID])) {
					$allowEditUIDExcluded = $args['allowEditUIDExcluded'][$peID];
				}
				if (!empty($args['allowEditDescriptionExcluded'][$peID])) {
					$allowEditDescriptionExcluded = $args['allowEditDescriptionExcluded'][$peID];
				}
				if (!empty($args['allowEditPathsExcluded'][$peID])) {
					$allowEditPathsExcluded = $args['allowEditPathsExcluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $attributePermission, $allowEditNameExcluded, $allowEditDateTimeExcluded, $allowEditUIDExcluded, $allowEditDescriptionExcluded, $allowEditPathsExcluded);
				$db->Execute('insert into PagePermissionPropertyAccessList (paID, peID, attributePermission, name, publicDateTime, uID, description, paths) values (?, ?, ?, ?, ?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['akIDInclude'])) { 
			foreach($args['akIDInclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into PagePermissionPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['akIDExclude'])) { 
			foreach($args['akIDExclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into PagePermissionPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
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
			$prow = $db->GetRow('select attributePermission, name, publicDateTime, uID, description, paths from PagePermissionPropertyAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
			if (is_array($prow) && $prow['attributePermission']) { 
				$l->setAttributesAllowedPermission($prow['attributePermission']);
				$l->setAllowEditName($prow['name']);
				$l->setAllowEditDateTime($prow['publicDateTime']);
				$l->setAllowEditUserID($prow['uID']);
				$l->setAllowEditDescription($prow['description']);
				$l->setAllowEditPaths($prow['paths']);
				$attributePermission = $prow['attributePermission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setAttributesAllowedPermission('A');
				$l->setAllowEditName(1);
				$l->setAllowEditDateTime(1);
				$l->setAllowEditUserID(1);
				$l->setAllowEditDescription(1);
				$l->setAllowEditPaths(1);
			} else {
				$l->setAttributesAllowedPermission('N');
				$l->setAllowEditName(0);
				$l->setAllowEditDateTime(0);
				$l->setAllowEditUserID(0);
				$l->setAllowEditDescription(0);
				$l->setAllowEditPaths(0);
			}
			if ($attributePermission == 'C') { 
				$akIDs = $db->GetCol('select akID from PagePermissionPropertyAttributeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
				$l->setAttributesAllowedArray($akIDs);
			}
		}
		return $list;
	}
	
}

class EditPagePropertiesPagePermissionAccessListItem extends PagePermissionAccessListItem {
	
	protected $customAttributeKeyArray = array();
	protected $attributesAllowedPermission = 'N';
	protected $allowEditName = 0;
	protected $allowEditDateTime = 0;
	protected $allowEditUID = 0;
	protected $allowEditDescription = 0;
	protected $allowEditPaths = 0;

	public function setAttributesAllowedPermission($permission) {
		$this->attributesAllowedPermission = $permission;
	}
	public function getAttributesAllowedPermission() {
		return $this->attributesAllowedPermission;
	}
	public function setAttributesAllowedArray($akIDs) {
		$this->customAttributeKeyArray = $akIDs;
	}
	public function getAttributesAllowedArray() {
		return $this->customAttributeKeyArray;
	}
	
	public function setAllowEditName($allow) {
		$this->allowEditName = $allow;
	}
	
	public function allowEditName() {
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

	public function setAllowEditPaths($allow) {
		$this->allowEditPaths = $allow;
	}
	
	public function allowEditPaths() {
		return $this->allowEditPaths;
	}
	
	
}