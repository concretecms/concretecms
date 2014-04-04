<?
namespace Concrete\Core\Permission\Response;
use Loader;
class Response {

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

		$className = \Concrete\Core\Foundation\ClassLoader::getClassName($object->getPermissionResponseClassName());
		$category = \Core\Permission\Key\Category\Category::getByHandle($object->getPermissionObjectKeyCategoryHandle());
		$object = new $className();
		$object->setPermissionCategoryObject($object);
		
		$pr->setPermissionObject($object);
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