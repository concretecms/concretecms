<?php
namespace Concrete\Core\Permission\Access;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use Loader;
use \Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
class EditPageThemePageAccess extends PageAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from PagePermissionThemeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into PagePermissionThemeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from PagePermissionThemeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['pThemeID']);
			$db->Execute('insert into PagePermissionThemeAccessListCustom  (peID, paID, pThemeID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from PagePermissionThemeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from PagePermissionThemeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['themesIncluded'])) {
			foreach($args['themesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['themesExcluded'])) {
			foreach($args['themesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into PagePermissionThemeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['pThemeIDInclude'])) {
			foreach($args['pThemeIDInclude'] as $peID => $pThemeIDs) {
				foreach($pThemeIDs as $pThemeID) {
					$v = array($this->getPermissionAccessID(), $peID, $pThemeID);
					$db->Execute('insert into PagePermissionThemeAccessListCustom (paID, peID, pThemeID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['pThemeIDExclude'])) {
			foreach($args['pThemeIDExclude'] as $peID => $pThemeIDs) {
				foreach($pThemeIDs as $pThemeID) {
					$v = array($this->getPermissionAccessID(), $peID, $pThemeID);
					$db->Execute('insert into PagePermissionThemeAccessListCustom (paID, peID, pThemeID) values (?, ?, ?)', $v);
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
			$prow = $db->GetRow('select permission from PagePermissionThemeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			if (is_array($prow) && $prow['permission']) {
				$l->setThemesAllowedPermission($prow['permission']);
				$permission = $prow['permission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setThemesAllowedPermission('A');
			} else {
				$l->setThemesAllowedPermission('N');
			}
			if ($permission == 'C') {
				$pThemeIDs = $db->GetCol('select pThemeID from PagePermissionThemeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setThemesAllowedArray($pThemeIDs);
			}
		}
		return $list;
	}

}
