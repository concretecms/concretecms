<?php
namespace Concrete\Core\Permission\Access;
use Loader;
use Concrete\Core\Permission\Key\Key as PermissionKey;
class ViewUserAttributesUserAccess extends UserAccess {

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['viewAttributesIncluded'])) {
			foreach($args['viewAttributesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['viewAttributesExcluded'])) {
			foreach($args['viewAttributesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['akIDInclude'])) {
			foreach($args['akIDInclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) {
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['akIDExclude'])) {
			foreach($args['akIDExclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) {
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}
	}

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into UserPermissionViewAttributeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['akID']);
			$db->Execute('insert into UserPermissionViewAttributeAccessListCustom  (peID, paID, akID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 'A';
			} else {
				$permission = $db->GetOne('select permission from UserPermissionViewAttributeAccessList where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setAttributesAllowedPermission($permission);
			if ($permission == 'C') {
				$akIDs = $db->GetCol('select akID from UserPermissionViewAttributeAccessListCustom where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				$l->setAttributesAllowedArray($akIDs);
			}
		}
		return $list;
	}

}
