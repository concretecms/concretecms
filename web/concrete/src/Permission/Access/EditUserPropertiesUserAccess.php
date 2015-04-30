<?php
namespace Concrete\Core\Permission\Access;
use Loader;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use \Concrete\Core\Permission\Key\UserKey as UserPermissionKey;
class EditUserPropertiesUserAccess extends UserAccess {

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

