<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class PermissionAccessEntity extends Object {
	
	public function getAccessEntityTypeID() {return $this->petID;}
	public function getAccessEntityTypeObject() {
		return PermissionAccessEntityType::getByID($this->petID);
	}
	public function getAccessEntityTypeHandle() {return $this->petHandle;}
	public function getAccessEntityID() {return $this->peID;}
	public function getAccessEntityLabel() {return $this->label;}
	abstract public function getAccessEntityUsers();
	abstract public function getAccessEntityTypeLinkHTML();
	
	final static function getByID($peID) {
		$db = Loader::db();
		$r = $db->GetRow('select petID, peID from PermissionAccessEntities where peID = ?', array($peID));
		if (is_array($r)) {
			$pt = PermissionAccessEntityType::getByID($r['petID']);
			$class = Loader::helper('text')->camelcase($pt->getAccessEntityTypeHandle());
			$class .= 'PermissionAccessEntity';
			$obj = new $class();
			$r['petHandle'] = $pt->getAccessEntityTypeHandle();
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

