<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionAccessEntity extends Object {
	
	public function getAccessEntityType() {return $this->peType;}
	public function getAccessEntityID() {return $this->peID;}
	public function getAccessEntityLabel() {return $this->label;}
	
	public static function getByID($peID) {
		$db = Loader::db();
		$r = $db->GetRow('select peType, peID from PermissionAccessEntities where peID = ?', array($peID));
		if (is_array($r)) {
			switch($r['peType']) {
				case 'U':
					$obj = new UserPermissionAccessEntity();
					break;
				case 'C':
					$obj = new GroupCombinationPermissionAccessEntity();
					break;
				default: // group
					$obj = new GroupPermissionAccessEntity();
					break;
			}
			$obj->setPropertiesFromArray($r);
			$obj->load();
		}
		return $obj;
	}
	
	public static function getForUser($user) {
		$entities = array();
		$db = Loader::db();
		if ($user->isRegistered()) { 
			// we find the peID for the current user, if one exists. This means that the user has special permissions set just for them.
			$peID = $db->GetOne('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityUsers paeg on pae.peID = paeg.peID where peType = ? and paeg.uID = ?', 
			array('U', $user->getUserID()));
			if ($peID > 0) {
				$entity = PermissionAccessEntity::getByID($peID);
				if (is_object($entity)) { 
					$entities[] = $entity;
				}
			}
		}
		
		// we find any group-specific ones (not combos)
		$ingids = array();
		foreach($user->getUserGroups() as $key => $val) {
			$ingids[] = $key;
		}
		$instr = implode(',',$ingids);
		$peIDs = $db->GetCol('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where peType = \'G\' and paeg.gID in (' . $instr . ')');
		if (is_array($peIDs)) {
			foreach($peIDs as $peID) { 
				$entity = PermissionAccessEntity::getByID($peID);
				if (is_object($entity)) { 
					$entities[] = $entity;
				}
			}
		}
		
		// finally, the most brutal one. we find any combos that this group would specifically be in.
		// first, we look for any combos that contain any of the groups this user is in. That way if there aren't any we can just skip it.
		if ($user->isRegistered()) { 
			$peIDs = $db->GetCol('select distinct pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where peType = \'C\' and paeg.gID in (' . $instr . ')');
			// now for each one we check to see if it applies
			foreach($peIDs as $peID) {
				$r = $db->GetRow('select count(gID) as peGroups, (select count(UserGroups.gID) from UserGroups where uID = ? and gID in (select gID from PermissionAccessEntityGroups where peID = ?)) as uGroups from PermissionAccessEntityGroups where peID = ?', array(
					$user->getUserID(), $peID, $peID));
				if ($r['peGroups'] == $r['uGroups'] && $r['peGroups'] > 1) { 
					$entity = PermissionAccessEntity::getByID($peID);
					if (is_object($entity)) { 
						$entities[] = $entity;
					}
				}
			}
		}
		return $entities;		
	}
}

class GroupPermissionAccessEntity extends PermissionAccessEntity {

	protected $group = false;
	public function getGroupObject() {return $this->group;}
	
	public static function getOrCreate(Group $g) {
		$db = Loader::db();
		$peID = $db->GetOne('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityGroups paeg on pae.peID = paeg.peID where peType = ? and paeg.gID = ?', 
			array('G', $g->getGroupID()));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (peType) values('G')");
			$peID = $db->Insert_ID();
			$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
		}
		return PermissionAccessEntity::getByID($peID);
	}
	
	public function load() {
		$db = Loader::db();
		$gID = $db->GetOne('select gID from PermissionAccessEntityGroups where peID = ?', array($this->peID));
		if ($gID) {
			$g = Group::getByID($gID);
			if (is_object($g)) {
				$this->group = $g;
				$this->label = $g->getGroupName();
			}
		}
	}
}

class GroupCombinationPermissionAccessEntity extends PermissionAccessEntity {
	
	protected $groups = array();
	
	public function getGroups() {
		return $this->groups;
	}
	
	public static function getOrCreate($groups) {
		$db = Loader::db();
		$q = 'select pae.peID from PermissionAccessEntities pae ';
		$i = 1;
		foreach($groups as $g) {
			$q .= 'left join PermissionAccessEntityGroups paeg' . $i . ' on pae.peID = paeg' . $i . '.peID ';
			$i++;
		}
		$q .= 'where peType = \'C\' ';
		$i = 1;
		foreach($groups as $g) {
			$q .= 'and paeg' . $i . '.gID = ' . $g->getGroupID() . ' ';
			$i++;
		}
		$peID = $db->GetOne($q);
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (peType) values('C')");
			$peID = $db->Insert_ID();
			foreach($groups as $g) {
				$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($peID, $g->getGroupID()));
			}
		}
		return PermissionAccessEntity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$gIDs = $db->GetCol('select gID from PermissionAccessEntityGroups where peID = ? order by gID asc', array($this->peID));
		if ($gIDs && is_array($gIDs)) {
			for ($i = 0; $i < count($gIDs); $i++) { 
				$g = Group::getByID($gIDs[$i]);
				if (is_object($g)) {
					$this->groups[] = $g;
					$this->label .= $g->getGroupName();
					if ($i + 1 < count($gIDs)) {
						$this->label .= t(' + ');
					}
				}
			}
		}
	}

}

class UserPermissionAccessEntity extends PermissionAccessEntity {

	protected $user;
	public function getUserObject() {return $this->user;}
	
	public static function getOrCreate(UserInfo $ui) {
		$db = Loader::db();
		$peID = $db->GetOne('select pae.peID from PermissionAccessEntities pae inner join PermissionAccessEntityUsers paeg on pae.peID = paeg.peID where peType = ? and paeg.uID = ?', 
			array('U', $ui->getUserID()));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (peType) values('U')");
			$peID = $db->Insert_ID();
			$db->Execute('insert into PermissionAccessEntityUsers (peID, uID) values (?, ?)', array($peID, $ui->getUserID()));
		}
		return PermissionAccessEntity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$uID = $db->GetOne('select uID from PermissionAccessEntityUsers where peID = ?', array($this->peID));
		if ($uID) {
			$ui = UserInfo::getByID($uID);
			if (is_object($ui)) {
				$this->user = $ui;
				$this->label .= $ui->getUserName();
			}
		}
	}

}

