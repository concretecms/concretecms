<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionCache {
	
	static $enabled = true;

	public static function disable() {
		PermissionCache::$enabled = false;
	}
	
	public static function getResponse($object) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
			if (array_key_exists($identifier, $cl->cache)) {
				return $cl->cache[$identifier];
			}
		}
	}

	public static function addResponse($object, PermissionResponse $pr) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
			$cl->cache[$identifier] = $pr;
		}
	}

	public static function getPermissionAccessObject($paID, PermissionKey $pk) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pao:' . $pk->getPermissionKeyID() . ':' . $paID;
			if (array_key_exists($identifier, $cl->cache)) {
				return $cl->cache[$identifier];
			}
		}
	}

	public static function addPermissionAccessObject($paID, PermissionKey $pk, $obj) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pao:' . $pk->getPermissionKeyID() . ':' . $paID;
			$cl->cache[$identifier] = $obj;
		}
	}
	
	public function validate(PermissionKey $pk) {
		if (!PermissionCache::$enabled) {
			return -1;
		}
		$cl = CacheLocal::get();
		if (!$cl->enabled) {
			return -1;
		}
		
		$object = $pk->getPermissionObject();
		if (is_object($object)) {
			$identifier = 'pk:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
		} else {
			$identifier = 'pk:' . $pk->getPermissionKeyHandle();
		}

		if (array_key_exists($identifier, $cl->cache)) {
			return $cl->cache[$identifier];
		}

		return -1;
	}

	public static function addValidate(PermissionKey $pk, $valid) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$object = $pk->getPermissionObject();
			if (is_object($object)) {
				$identifier = 'pk:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
			} else {
				$identifier = 'pk:' . $pk->getPermissionKeyHandle();
			}
			$cl->cache[$identifier] = $valid;
		}
	}
	
	public static function addAccessObject(PermissionKey $pk, $object, $pa) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pka:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
			$cl->cache[$identifier] = $pa;
		}
	}

	public static function clearAccessObject(PermissionKey $pk, $object) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pka:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
			unset($cl->cache[$identifier]);
		}
	}

	public static function getAccessObject($pk, $object) {
		if (!PermissionCache::$enabled) {
			return false;
		}
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pka:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
			if (array_key_exists($identifier, $cl->cache)) {
				return $cl->cache[$identifier];
			}
		}
		return false;
	}
	



}