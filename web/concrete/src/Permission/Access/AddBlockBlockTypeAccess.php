<?php
namespace Concrete\Core\Permission\Access;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use Loader;
use Page;
use PermissionKey;
class AddBlockBlockTypeAccess extends BlockTypeAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from BlockTypePermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into BlockTypePermissionBlockTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['btID']);
			$db->Execute('insert into BlockTypePermissionBlockTypeAccessListCustom  (peID, paID, btID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from BlockTypePermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['blockTypesIncluded'])) {
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into BlockTypePermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['blockTypesExcluded'])) {
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into BlockTypePermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) {
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) {
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into BlockTypePermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) {
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) {
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into BlockTypePermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}
	}

	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 'A';
			} else {
				$permission = $db->GetOne('select permission from BlockTypePermissionBlockTypeAccessList where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') {
				$btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				$l->setBlockTypesAllowedArray($btIDs);
			}
		}
		return $list;
	}

}
