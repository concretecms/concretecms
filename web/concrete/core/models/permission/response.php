<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PermissionResponse {

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
	
	public static function getResponse($object) {
		$r = PermissionCache::getResponse($object);
		if (is_object($r)) {
			return $r;
		}
		
		if (method_exists($object, 'getPermissionObjectPermissionKeyCategoryHandle')) {
			$objectClass = Loader::helper('text')->camelcase($object->getPermissionObjectPermissionKeyCategoryHandle());
			$handle = $object->getPermissionObjectPermissionKeyCategoryHandle();
		} else {
			$objectClass = get_class($object);
			$handle = Loader::helper('text')->uncamelcase($objectClass);
		}
		$category = PermissionKeyCategory::getByHandle($handle);
		$c1 = $objectClass . 'PermissionResponse';
		if (!is_object($category)) {
			if ($object instanceof Page) {
				$category = PermissionKeyCategory::getByHandle('page');
				$c1 = 'PagePermissionResponse';
			} else if ($object instanceof Area) {
				$category = PermissionKeyCategory::getByHandle('area');
				$c1 = 'AreaPermissionResponse';
			}
		}
		$pr = new $c1();
		$pr->setPermissionObject($object);
		$pr->setPermissionCategoryObject($category);
		
		PermissionCache::addResponse($object, $pr);
		
		return $pr;
	}
	
	public function validate($permission, $args = array()) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}
		if (!is_object($this->category)) {
			throw new Exception(t('Unable to get category for permission %s', $permission));
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