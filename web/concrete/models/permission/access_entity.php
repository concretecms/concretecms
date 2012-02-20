<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionsAccessEntity extends Object {
	
	public function getAccessEntityType() {return $this->peType;}
	public function getAccessEntityID() {return $this->peID;}
	
	public static function getByID($peID) {
		$db = Loader::db();
		$r = $db->GetRow('select peType, peID from PermissionsAccessEntities where peID = ?', array($peID));
		if (is_array($r)) {
			switch($r['peType']) {
				case 'U':
					$obj = new UserPermissionsAccessEntity();
					break;
				case 'C':
					$obj = new GroupCombinationPermissionsAccessEntity();
					break;
				default: // group
					$obj = new GroupPermissionsAccessEntity();
					break;
			}
			$obj->setPropertiesFromArray($r);
			$obj->load();
		}
		return $obj;
	}

}

class GroupPermissionsAccessEntity extends PermissionsAccessEntity {

	protected $group = false;
	
	public static function create(Group $g) {
		$db = Loader::db();
		$db->Execute("insert into PermissionsAccessEntities (peType) values('G')");
		$id = $db->Insert_ID();
		$db->Execute('insert into PermissionsAccessEntityGroups (peID, gID) values (?, ?)', array($id, $g->getGroupID()));
		return PermissionsAccessEntity::getByID($id);
	}
	
	public function load() {
		$db = Loader::db();
		$gID = $db->GetOne('select gID from PermissionsAccessEntityGroups where peID = ?', array($this->peID));
		if ($gID) {
			$g = Group::getByID($gID);
			if (is_object($g)) {
				$this->group = $g;
			}
		}
	}
}

class GroupCombinationPermissionsAccessEntity extends PermissionsAccessEntity {
	
	protected $groups = array();
	
	public static function create($groups) {
		$db = Loader::db();
		$db->Execute("insert into PermissionsAccessEntities (peType) values('C')");
		$id = $db->Insert_ID();
		foreach($groups as $g) {
			$db->Execute('insert into PermissionsAccessEntityGroups (peID, gID) values (?, ?)', array($id, $g->getGroupID()));
		}
		return PermissionsAccessEntity::getByID($id);
	}

	public function load() {
		$db = Loader::db();
		$gIDs = $db->GetCol('select gID from PermissionsAccessEntityGroups where peID = ?', array($this->peID));
		if ($gIDs && is_array($gIDs)) {
			foreach($gIDs as $gID) {
				$g = Group::getByID($gID);
				if (is_object($g)) {
					$this->groups[] = $g;
				}
			}
		}
	}

}

class UserPermissionsAccessEntity extends PermissionsAccessEntity {

	protected $user;
	
	public static function create(UserInfo $ui) {
		$db = Loader::db();
		$db->Execute("insert into PermissionsAccessEntities (peType) values('U')");
		$id = $db->Insert_ID();
		$db->Execute('insert into PermissionsAccessEntityUsers (peID, uID) values (?, ?)', array($id, $ui->getUserID()));
		return PermissionsAccessEntity::getByID($id);
	}

	public function load() {
		$db = Loader::db();
		$uID = $db->GetOne('select uID from PermissionsAccessEntityUsers where peID = ?', array($this->peID));
		if ($uID) {
			$ui = UserInfo::getByID($uID);
			if (is_object($ui)) {
				$this->user = $ui;
			}
		}
	}

}

