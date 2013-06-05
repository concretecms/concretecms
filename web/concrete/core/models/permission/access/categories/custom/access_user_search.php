<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AccessUserSearchUserPermissionAccess extends PermissionAccess {

	public function duplicate($newPA = false) {
		$db = Loader::db();
		$newPA = parent::duplicate($newPA);
		$r = $db->Execute('select * from ' . $this->dbTableAccessList . ' where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into ' . $this->dbTableAccessList . ' (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from ' . $this->dbTableAccessListCustom . ' where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['gID']);
			$db->Execute('insert into ' . $this->dbTableAccessListCustom . ' (peID, paID, gID) values (?, ?, ?)', $v);
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
				$permission = $db->GetOne('select permission from ' . $this->dbTableAccessList . ' where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setGroupsAllowedPermission($permission);
			if ($permission == 'C') { 
				$gIDs = $db->GetCol('select gID from ' . $this->dbTableAccessListCustom . ' where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setGroupsAllowedArray($gIDs);
			}
		}
		return $list;
	}

	protected $dbTableAccessList = 'UserPermissionUserSearchAccessList';
	protected $dbTableAccessListCustom = 'UserPermissionUserSearchAccessListCustom';

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from ' . $this->dbTableAccessList . ' where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from ' . $this->dbTableAccessListCustom . ' where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['groupsIncluded'])) { 
			foreach($args['groupsIncluded'] as $peID => $permission) {
				$v = array($peID, $this->getPermissionAccessID(), $permission);
				$db->Execute('insert into ' . $this->dbTableAccessList . ' (peID, paID, permission) values (?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['groupsExcluded'])) { 
			foreach($args['groupsExcluded'] as $peID => $permission) {
				$v = array($peID, $this->getPermissionAccessID(), $permission);
				$db->Execute('insert into ' . $this->dbTableAccessList . ' (peID, paID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['gIDInclude'])) { 
			foreach($args['gIDInclude'] as $peID => $gIDs) {
				foreach($gIDs as $gID) { 
				$v = array($peID, $this->getPermissionAccessID(), $gID);
					$db->Execute('insert into ' . $this->dbTableAccessListCustom . ' (peID, paID, gID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['gIDExclude'])) { 
			foreach($args['gIDExclude'] as $peID => $gIDs) {
				foreach($gIDs as $gID) { 
				$v = array($peID, $this->getPermissionAccessID(), $gID);
					$db->Execute('insert into ' . $this->dbTableAccessListCustom . ' (peID, paID, gID) values (?, ?, ?)', $v);
				}
			}
		}
	}
	
}