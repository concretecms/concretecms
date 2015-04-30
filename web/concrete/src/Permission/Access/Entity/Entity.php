<?php
namespace Concrete\Core\Permission\Access\Entity;
use \Concrete\Core\Foundation\Object;
use Loader;
use PermissionAccess;
use CacheLocal;
use Core;
abstract class Entity extends Object {

	public function getAccessEntityTypeID() {return $this->petID;}
	public function getAccessEntityTypeObject() {
		return Type::getByID($this->petID);
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
		$obj = CacheLocal::getEntry('permission_access_entity', $peID);
		if ($obj instanceof PermissionAccessEntity) {
			return $obj;
		}
		$db = Loader::db();
		$r = $db->GetRow('select petID, peID from PermissionAccessEntities where peID = ?', array($peID));
		if (is_array($r)) {
			$pt = Type::getByID($r['petID']);
			if (!is_object($pt)) {
				return false;
			}

			$className = '\\Concrete\\Core\\Permission\\Access\\Entity\\' . Loader::helper('text')->camelcase($pt->getAccessEntityTypeHandle()) . 'Entity';
			$obj = Core::make($className);
			$r['petHandle'] = $pt->getAccessEntityTypeHandle();
			$obj->setPropertiesFromArray($r);
			$obj->load();
		}
		CacheLocal::set('permission_access_entity', $peID, $obj);
		return $obj;
	}

	public static function getForUser($user) {
		$entities = array();
		$db = Loader::db();
		$types = Type::getList();
		foreach($types as $t) {
			$entities = array_merge($entities, $t->getAccessEntitiesForUser($user));
		}
		return $entities;
	}
}

