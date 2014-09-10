<?php
namespace Concrete\Core\Permission\Key;
use Loader;
use User;
use \Concrete\Core\Permission\Duration as PermissionDuration;
class EditUserPropertiesUserKey extends UserKey  {

	public function getMyAssignment() {
		$u = new User();
		$asl = new \Concrete\Core\Permission\Access\ListItem\EditUserPropertiesUserListItem();

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
		$list = $this->getAccessListItems(UserKey::ACCESS_TYPE_ALL, $accessEntities);
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
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditUserName()) {
				$asl->setAllowEditUserName(0);
				$excluded[] = 'uName';
			}
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditEmail()) {
				$asl->setAllowEditEmail(0);
				$excluded[] = 'uEmail';
			}
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditPassword()) {
				$asl->setAllowEditPassword(0);
				$excluded[] = 'uPassword';
			}
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditAvatar()) {
				$asl->setAllowEditAvatar(0);
				$excluded[] = 'uAvatar';
			}
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditTimezone()) {
				$asl->setAllowEditTimezone(0);
				$excluded[] = 'uTimezone';
			}
			if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE && !$l->allowEditDefaultLanguage()) {
				$asl->setAllowEditDefaultLanguage(0);
				$excluded[] = 'uDefaultLanguage';
			}
			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
				if ($l->getAccessType() == UserKey::ACCESS_TYPE_EXCLUDE) {
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
