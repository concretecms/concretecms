<?
defined('C5_EXECUTE') or die("Access Denied.");

class EditUserPropertiesUserPermissionKey extends UserPermissionKey  {

	public function getMyAssignment() {
		$u = new User();
		$asl = new EditUserPropertiesUserPermissionAccessListItem();
		
		$db = Loader::db();
		$allAKIDs = $db->GetCol('select akID from UserAttributeKeys order by akID asc');

		if ($u->isSuperUser()) {
			$asl->setAllowEditUserName(1);
			$asl->setAllowEditEmail(1);
			$asl->setAllowEditPassword(1);
			$asl->setAllowEditAvatar(1);
			$asl->setAllowEditTimezone(1);
			$asl->setAllowEditDefaultLanguage(1);
			$asl->setAttributesAllowedArray($allAKIDs);
			$asl->setAttributesAllowedPermission('A');
			return $asl;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return $asl;
		}
		
		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);
		$properties = array();
		
		$excluded = array();
		$akIDs = array();
		$u = new User();
		foreach($list as $l) {

			if ($l->allowEditUserName() && (!in_array('uName', $excluded))) {
				$asl->setAllowEditUserName(1);
			}
			if ($l->allowEditEmail() && (!in_array('uEmail', $excluded))) {
				$asl->setAllowEditEmail(1);
			}
			if ($l->allowEditPassword() && (!in_array('uPassword', $excluded))) {
				$asl->setAllowEditPassword(1);
			}
			if ($l->allowEditAvatar() && (!in_array('uAvatar', $excluded))) {
				$asl->setAllowEditAvatar(1);
			}
			if ($l->allowEditTimezone() && (!in_array('uTimezone', $excluded))) {
				$asl->allowEditTimezone(1);
			}
			if ($l->allowEditDefaultLanguage() && (!in_array('uDefaultLanguage', $excluded))) {
				$asl->setAllowEditDefaultLanguage(1);
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditUserName()) {
				$asl->setAllowEditUserName(0);
				$excluded[] = 'uName';
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditEmail()) {
				$asl->setAllowEditEmail(0);
				$excluded[] = 'uEmail';
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditPassword()) {
				$asl->setAllowEditPassword(0);
				$excluded[] = 'uPassword';
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditAvatar()) {
				$asl->setAllowEditAvatar(0);
				$excluded[] = 'uAvatar';
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditTimezone()) {
				$asl->setAllowEditTimezone(0);
				$excluded[] = 'uTimezone';
			}
			if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditDefaultLanguage()) {
				$asl->setAllowEditDefaultLanguage(0);
				$excluded[] = 'uDefaultLanguage';
			}
			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
				if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_EXCLUDE) {
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
		
		$asl = $this->getMyAssignment();

		if (is_object($obj)) {
			if ($obj instanceof UserAttributeKey) {
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
			$asl->allowEditUserName() || 
			$asl->allowEditAvatar() || 
			$asl->allowEditEmail() || 
			$asl->allowEditPassword() || 
			$asl->allowEditTimezone() || 
			$asl->allowEditDefaultLanguage() || 
			($asl->getAttributesAllowedPermission() == 'A' || ($asl->getAttributesAllowedPermission() == 'C' && count($asl->getAttributesAllowedArray() > 0)))) {
				return true;
		} else {
			return false;
		}
	}
	
	
}

class EditUserPropertiesUserPermissionAccess extends UserPermissionAccess {
	
	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from UserPermissionEditPropertyAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($newPA->getPermissionAccessID(), 
			$row['peID'], 
			$row['attributePermission'],
			$row['uName'],
			$row['uEmail'],
			$row['uPassword'],
			$row['uAvatar'],
			$row['uTimezone'],
			$row['uDefaultLanguage']			
			);
			$db->Execute('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from UserPermissionEditPropertyAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['akID']);
			$db->Execute('insert into UserPermissionEditPropertyAttributeAccessListCustom (peID, paID, akID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}
	
	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from UserPermissionEditPropertyAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from UserPermissionEditPropertyAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['propertiesIncluded'])) { 
			foreach($args['propertiesIncluded'] as $peID => $attributePermission) {
				$allowEditUName = 0;
				$allowEditUEmail = 0;
				$allowEditUPassword = 0;
				$allowEditUAvatar = 0;
				$allowEditUTimezone = 0;
				$allowEditUDefaultLanguage = 0;
				if (!empty($args['allowEditUName'][$peID])) {
					$allowEditUName = $args['allowEditUName'][$peID];
				}
				if (!empty($args['allowEditUEmail'][$peID])) {
					$allowEditUEmail = $args['allowEditUEmail'][$peID];
				}
				if (!empty($args['allowEditUPassword'][$peID])) {
					$allowEditUPassword = $args['allowEditUPassword'][$peID];
				}
				if (!empty($args['allowEditUAvatar'][$peID])) {
					$allowEditUAvatar = $args['allowEditUAvatar'][$peID];
				}
				if (!empty($args['allowEditUTimezone'][$peID])) {
					$allowEditUTimezone = $args['allowEditUTimezone'][$peID];
				}
				if (!empty($args['allowEditUDefaultLanguage'][$peID])) {
					$allowEditUDefaultLanguage = $args['allowEditUDefaultLanguage'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $attributePermission, $allowEditUName, $allowEditUEmail, $allowEditUPassword, $allowEditUAvatar, $allowEditUTimezone, $allowEditUDefaultLanguage);
				$db->Execute('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['propertiesExcluded'])) { 
			foreach($args['propertiesExcluded'] as $peID => $attributePermission) {
				$allowEditUNameExcluded = 0;
				$allowEditUEmailExcluded = 0;
				$allowEditUPasswordExcluded = 0;
				$allowEditUAvatarExcluded = 0;
				$allowEditUTimezoneExcluded = 0;
				$allowEditUDefaultLanguageExcluded = 0;
				if (!empty($args['allowEditUNameExcluded'][$peID])) {
					$allowEditUNameExcluded = $args['allowEditUNameExcluded'][$peID];
				}
				if (!empty($args['allowEditUEmailExcluded'][$peID])) {
					$allowEditUEmailExcluded = $args['allowEditUEmailExcluded'][$peID];
				}
				if (!empty($args['allowEditUPasswordExcluded'][$peID])) {
					$allowEditUPasswordExcluded = $args['allowEditUPasswordExcluded'][$peID];
				}
				if (!empty($args['allowEditUAvatarExcluded'][$peID])) {
					$allowEditUAvatarExcluded = $args['allowEditUAvatarExcluded'][$peID];
				}
				if (!empty($args['allowEditUTimezoneExcluded'][$peID])) {
					$allowEditUTimezoneExcluded = $args['allowEditUTimezoneExcluded'][$peID];
				}
				if (!empty($args['allowEditUDefaultLanguageExcluded'][$peID])) {
					$allowEditUDefaultLanguageExcluded = $args['allowEditUDefaultLanguageExcluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $attributePermission, $allowEditUNameExcluded, $allowEditUEmailExcluded, $allowEditUPasswordExcluded, $allowEditUAvatarExcluded, $allowEditUTimezoneExcluded, $allowEditUDefaultLanguageExcluded);
				$db->Execute('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['akIDInclude'])) { 
			foreach($args['akIDInclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionEditPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['akIDExclude'])) { 
			foreach($args['akIDExclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) { 
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionEditPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}
	}

	public function getAccessListItems($accessType = UserPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$prow = $db->GetRow('select attributePermission, uName, uPassword, uEmail, uAvatar, uTimezone, uDefaultLanguage from UserPermissionEditPropertyAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
			if (is_array($prow) && $prow['attributePermission']) { 
				$l->setAttributesAllowedPermission($prow['attributePermission']);
				$l->setAllowEditUserName($prow['uName']);
				$l->setAllowEditEmail($prow['uEmail']);
				$l->setAllowEditPassword($prow['uPassword']);
				$l->setAllowEditAvatar($prow['uAvatar']);
				$l->setAllowEditTimezone($prow['uTimezone']);
				$l->setAllowEditDefaultLanguage($prow['uDefaultLanguage']);
				$attributePermission = $prow['attributePermission'];
			} else if ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setAttributesAllowedPermission('A');
				$l->setAllowEditUserName(1);
				$l->setAllowEditEmail(1);
				$l->setAllowEditPassword(1);
				$l->setAllowEditAvatar(1);
				$l->setAllowEditTimezone(1);
				$l->setAllowEditDefaultLanguage(1);
			} else {
				$l->setAttributesAllowedPermission('N');
				$l->setAllowEditUserName(0);
				$l->setAllowEditEmail(0);
				$l->setAllowEditPassword(0);
				$l->setAllowEditAvatar(0);
				$l->setAllowEditTimezone(0);
				$l->setAllowEditDefaultLanguage(0);
			}
			if ($attributePermission == 'C') { 
				$akIDs = $db->GetCol('select akID from UserPermissionEditPropertyAttributeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));
				$l->setAttributesAllowedArray($akIDs);
			}
		}
		return $list;
	}
}


class EditUserPropertiesUserPermissionAccessListItem extends PermissionAccessListItem {
	
	protected $customAttributeKeyArray = array();
	protected $attributesAllowedPermission = 'N';
	protected $allowEditUName = 0;
	protected $allowEditUEmail = 0;
	protected $allowEditUPassword = 0;
	protected $allowEditUAvatar = 0;
	protected $allowEditTimezone = 0;
	protected $allowEditDefaultLanguage = 0;

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
	
	public function setAllowEditUserName($allow) {
		$this->allowEditUName = $allow;
	}
	
	public function allowEditUserName() {
		return $this->allowEditUName;
	}

	public function setAllowEditEmail($allow) {
		$this->allowEditUEmail = $allow;
	}
	
	public function allowEditEmail() {
		return $this->allowEditUEmail;
	}

	public function setAllowEditPassword($allow) {
		$this->allowEditUPassword = $allow;
	}
	
	public function allowEditPassword() {
		return $this->allowEditUPassword;
	}

	public function setAllowEditAvatar($allow) {
		$this->allowEditUAvatar = $allow;
	}
	
	public function allowEditAvatar() {
		return $this->allowEditUAvatar;
	}
	
	public function setAllowEditTimezone($allow) {
		$this->allowEditUTimezone = $allow;
	}
	
	public function allowEditTimezone() {
		return $this->allowEditUTimezone;
	}

	public function setAllowEditDefaultLanguage($allow) {
		$this->allowEditUDefaultLanguage = $allow;
	}
	
	public function allowEditDefaultLanguage() {
		return $this->allowEditUDefaultLanguage;
	}
	
	
}