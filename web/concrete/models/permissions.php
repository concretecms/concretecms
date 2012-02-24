<?

defined('C5_EXECUTE') or die("Access Denied.");

class Permissions {

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
	

	public function __construct($object) {
		$handle = Loader::helper('text')->uncamelcase(get_class($object));
		$this->response = PermissionResponse::getResponse($handle, $object);
		$r = $this->response->testForErrors();
		if ($r) {
			$this->error = $r;
		}
	}
	
	/** 
	 * We take any permissions function run on the permissions class and send it into the category
	 * object
	 */
	public function __call($f, $a) {
		return $this->response->{$f}();
	}
	
}