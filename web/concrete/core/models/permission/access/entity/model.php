<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Model_PermissionAccessEntity extends Object {
	
	public function getAccessEntityTypeID() {return $this->petID;}
	public function getAccessEntityTypeObject() {
		return PermissionAccessEntityType::getByID($this->petID);
	}
	public function getAccessEntityTypeHandle() {return $this->petHandle;}
	public function getAccessEntityID() {return $this->peID;}
	public function getAccessEntityLabel() {return $this->label;}
	abstract public function getAccessEntityUsers(PermissionAccess $pa);
	abstract public function getAccessEntityTypeLinkHTML();
	abstract public static function getAccessEntitiesForUser($user);
	
	public function validate(PermissionAccess $pae) {
		return true;	
	}
	
	final static function getByID($peID) {
		$obj = Cache::get('permission_access_entity', $peID);
		if ($obj instanceof PermissionAccessEntity) {
			return $obj;
		}
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
		Cache::set('permission_access_entity', $peID, $obj);
		return $obj;
	}
	
	public static function getForUser($user) {
		$entities = array();
		$db = Loader::db();
		$types = PermissionAccessEntityType::getList();
		foreach($types as $t) {
			$entities = array_merge($entities, $t->getAccessEntitiesForUser($user));			
		}
		return $entities;
	}
}

