<?php
namespace Concrete\Core\Permission\Access;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Page\Page;
use Concrete\Core\Area\Area;
use Loader;
class AddBlockToAreaAreaAccess extends AreaAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into AreaPermissionBlockTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['btID']);
			$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom  (peID, paID, btID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function getAccessListItems($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		$pobj = $this->getPermissionObjectToCheck();
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($pobj instanceof Page) {
				$permission = $db->GetOne('select permission from BlockTypePermissionBlockTypeAccessList where paID = ?', array($l->getPermissionAccessID()));
			} else {
				$permission = $db->GetOne('select permission from AreaPermissionBlockTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			}
			if ($permission != 'N' && $permission != 'C') {
				$permission = 'A';
			}
			$l->setBlockTypesAllowedPermission($permission);
			if ($permission == 'C') {
				if ($pobj instanceof Area) {
					$btIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				} else {
					$btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($l->getPermissionAccessID()));
				}
				$l->setBlockTypesAllowedArray($btIDs);
			}
		}
		return $list;
	}

	public function save($args) {
		$db = Loader::db();
		parent::save();
		$db->Execute('delete from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['blockTypesIncluded'])) {
			foreach($args['blockTypesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['blockTypesExcluded'])) {
			foreach($args['blockTypesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['btIDInclude'])) {
			foreach($args['btIDInclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) {
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['btIDExclude'])) {
			foreach($args['btIDExclude'] as $peID => $btIDs) {
				foreach($btIDs as $btID) {
					$v = array($this->getPermissionAccessID(), $peID, $btID);
					$db->Execute('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
				}
			}
		}
	}

}
