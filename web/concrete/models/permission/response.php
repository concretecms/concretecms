<?
defined('C5_EXECUTE') or die("Access Denied.");
class PermissionResponse {

	protected $object;
	protected $allowedPermissions = array();
	protected $category;
	
	public function setPermissionObject($object) { 
		$this->object = $object;
	}
	
	public function setPermissionCategoryObject($category) {
		$this->category = $category;
	}
	
	public function testForErrors() { }
	
	public function loadSuperUserPermissions() {
		$u = new User();
		$db = Loader::db();
		if ($u->isSuperUser()) {
			// all permissions
			$this->allowedPermissions = $db->GetCol('select pkHandle from PermissionKeys');
		}
	}
	
	public function getAllowedPermissions() {
		return $this->allowedPermissions;
	}
	
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
		$pr->loadPermissions();
		return $pr;
	}
	
	public function __call($f, $a) {
		$permission = substr($f, 3);
		$permission = Loader::helper('text')->uncamelcase($permission);
		return in_array($permission, $this->getAllowedPermissions());
	}
	

}