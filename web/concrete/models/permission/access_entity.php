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

}

class GroupPermissionAccessEntity extends PermissionAccessEntity {

	protected $group = false;
	public function getGroupObject() {return $this->group;}
	
	public static function create(Group $g) {
		$db = Loader::db();
		$db->Execute("insert into PermissionAccessEntities (peType) values('G')");
		$id = $db->Insert_ID();
		$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($id, $g->getGroupID()));
		return PermissionAccessEntity::getByID($id);
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
	
	public static function create($groups) {
		$db = Loader::db();
		$db->Execute("insert into PermissionAccessEntities (peType) values('C')");
		$id = $db->Insert_ID();
		foreach($groups as $g) {
			$db->Execute('insert into PermissionAccessEntityGroups (peID, gID) values (?, ?)', array($id, $g->getGroupID()));
		}
		return PermissionAccessEntity::getByID($id);
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
	
	public static function create(UserInfo $ui) {
		$db = Loader::db();
		$db->Execute("insert into PermissionAccessEntities (peType) values('U')");
		$id = $db->Insert_ID();
		$db->Execute('insert into PermissionAccessEntityUsers (peID, uID) values (?, ?)', array($id, $ui->getUserID()));
		return PermissionAccessEntity::getByID($id);
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

