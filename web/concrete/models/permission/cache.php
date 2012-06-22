<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionCache {
	
	public static function getResponse($object) {
		$cl = CacheLocal::get();
		$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
		if (array_key_exists($identifier, $cl->cache)) {
			return $cl->cache[$identifier];
		}
	}

	public static function addResponse($object, PermissionResponse $pr) {
		$cl = CacheLocal::get();
		$identifier = 'pr:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
		$cl->cache[$identifier] = $pr;
	}
	
	public function validate(PermissionKey $pk) {
		$object = $pk->getPermissionObject();
		$cl = CacheLocal::get();
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
		if (is_object($object)) {
			$identifier = 'pk:' . $pk->getPermissionKeyHandle() . ':' . $object->getPermissionObjectIdentifier();
		} else {
			$identifier = 'pk:' . $pk->getPermissionKeyHandle();
		}
		$cl->cache[$identifier] = $valid;
	}
	


}