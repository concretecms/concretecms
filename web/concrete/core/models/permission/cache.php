<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionCache {
	
	public static function getResponse($object) {
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
			if (array_key_exists($identifier, $cl->cache)) {
				return $cl->cache[$identifier];
			}
		}
	}

	public static function addResponse($object, PermissionResponse $pr) {
		$cl = CacheLocal::get();
		if ($cl->enabled) {
			$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
			$cl->cache[$identifier] = $pr;
		}
	}
	
	public function validate(PermissionKey $pk) {
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
	


}