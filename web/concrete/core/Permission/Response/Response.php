<?
namespace Concrete\Core\Permission\Response;
use Aws\CloudFront\Exception\Exception;
use Loader;
use Page;
use User;
use Group;
use PermissionKey;
use PermissionKeyCategory;
use Permissions;
use Core;
use \Concrete\Core\Permission\Cache as PermissionCache;
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

		$className = $object->getPermissionResponseClassName();
		if ($object->getPermissionObjectKeyCategoryHandle()) {
			$category = PermissionKeyCategory::getByHandle($object->getPermissionObjectKeyCategoryHandle());
		}
		$pr = Core::make($className);
		$pr->setPermissionCategoryObject($category);
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
			throw new \Exception(t('Unable to get category for permission %s', $permission));
		}
		$pk = $this->category->getPermissionKeyByHandle($permission);
		if (!$pk) {
			throw new Exception(t('Unable to get permission key for %s', $permission));
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
