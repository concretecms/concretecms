<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionResponse {

	protected $object;
	protected $allowedPermissions = array();
	protected $customClassObjects = array();
	protected $category;
	
	public function setPermissionObject($object) { 
		$this->object = $object;
	}
	
	public function setPermissionCategoryObject($category) {
		$this->category = $category;
	}
	
	public function testForErrors() { }
	
	
	public static function getResponse($handle, $args) {
		$category = PermissionKeyCategory::getByHandle($handle);
		if (is_object($category) && $category->getPackageID() > 0) { 
			Loader::model('permission/response/' . $handle, $category->getPackageHandle());
		} else {
			Loader::model('permission/response/' . $handle);
		}
		$txt = Loader::helper('text');
		$className = $txt->camelcase($handle) . 'PermissionResponse';
		if (class_exists($className)) { 
			$c1 = $className;	
		} else { 
			$c1 = 'PermissionResponse';
		}
		$pr = new $c1();
		$pr->setPermissionObject($args);
		$pr->setPermissionCategoryObject($category);
		return $pr;
	}
	
	public function validate($permission, $args = array()) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$pk = $this->category->getPermissionKeyByHandle($permission);
		$pk->setPermissionObject($this->object);
		return call_user_func_array(array($pk, 'validate'), $args);
	}
	
	public function __call($f, $a) {
		$permission = substr($f, 3);
		$permission = Loader::helper('text')->uncamelcase($permission);
		return $this->validate($permission, $a);
	}
	

}