<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_Permissions {

	protected $response;
	
	/** 
	 * Checks to see if there is a fatal error with this particular permission call.
	 */
	public function isError() {
		return $this->error != '';
	}
	
	/** 
	 * Returns the error code if there is one
	 */
	public function getError() {
		return $this->error;
	}
	
	/** 
	 * Legacy
	 * @private
	 */
	public function getOriginalObject() {
		return $this->response->getPermissionObject();
	}
	

	public function __construct($object = false) {
		if ($object) { 
			$this->response = PermissionResponse::getResponse($object);
			$r = $this->response->testForErrors();
			if ($r) {
				$this->error = $r;
			}
		}
	}
	
	public function getResponseObject() {
		return $this->response;
	}
	
	/** 
	 * We take any permissions function run on the permissions class and send it into the category
	 * object
	 */
	public function __call($f, $a) {
		if (!is_object($this->response)) {
			// handles task permissions
			$permission = Loader::helper('text')->uncamelcase($f);
		}

		if (count($a) > 0) { 
			if (is_object($this->response)) { 
				$r = call_user_func_array(array($this->response, $f), $a);
			} else {
				$pk = PermissionKey::getByHandle($permission);
				$r = call_user_func_array(array($pk, $f), $a);
			}
		} else { 
			if (is_object($this->response)) { 
				$r = $this->response->{$f}();
			} else {
				$pk = PermissionKey::getByHandle($permission);
				if (is_object($pk)) {
					$r = $pk->validate();
				} else {
					throw new Exception(t('Unable to get permission key for %s', $permission));
				}
			}
		}
		
		if (is_array($r) || is_object($r)) {
			return $r;
		} else if ($r) {
			return 1;
		} else {
			return 0;
		}
	}
	
}