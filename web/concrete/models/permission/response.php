<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionResponse {

	protected $object;
	protected $allowedPermissions = array();
	protected $customClassObjects = array();
	protected $category;
	static $cache = array();
	
	public function setPermissionObject($object) { 
		$this->object = $object;
	}
	public function getPermissionObject() {
		return $this->object;
	}
	public function setPermissionCategoryObject($category) {
		$this->category = $category;
	}
	
	public function testForErrors() { }
	
	public static function getFromCache($object) {
		$cl = CacheLocal::get();
		$identifier = 'PermissionResponse:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
		if (array_key_exists($identifier, $cl->cache)) {
			return $cl->cache[$identifier];
		}
	}

	public static function addToCache($object, PermissionResponse $pr) {
		$cl = CacheLocal::get();
		$identifier = 'PermissionResponse:' . get_class($object) . ':' . $object->getPermissionObjectIdentifier();
		$cl->cache[$identifier] = $pr;
	}
	
	public static function getResponse($object) {
		$r = self::getFromCache($object);
		if (is_object($r)) {
			return $r;
		}
		
		$category = PermissionKeyCategory::getByHandle(Loader::helper('text')->uncamelcase(get_class($object)));
		$txt = Loader::helper('text');
		$c1 = get_class($object) . 'PermissionResponse';
		$pr = new $c1();
		$pr->setPermissionObject($object);
		$pr->setPermissionCategoryObject($category);
		
		self::addToCache($object, $pr);
		
		return $pr;
	}
	
	public function validate($permission, $args = array()) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$pk = $this->category->getPermissionKeyByHandle($permission);
		if (!$pk) {
			print t('Unable to get permission key for %s', $permission);
			exit;
		}
		$pk->setPermissionObject($this->object);
		return call_user_func_array(array($pk, 'validate'), $args);
	}
	
	public function __call($f, $a) {
		$permission = substr($f, 3);
		$permission = Loader::helper('text')->uncamelcase($permission);
		return $this->validate($permission, $a);
	}
	

}