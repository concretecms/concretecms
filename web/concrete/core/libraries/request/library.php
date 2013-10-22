<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents a particular request to the Concrete-powered website. The request object then determines what is being requested, based on the path, and presents itself to the rest of the dispatcher (which loads the page, etc...)
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Library_Request extends \Symfony\Component\HttpFoundation\Request {

	static $_request = null;

	public function getCurrentPage() {
		global $c;
		return $c;
	}

	public static function getInstance() {
		if (null === self::$_request) {
			self::$_request = Request::createFromGlobals();
		}
		return self::$_request;
	}

	/** 
	 * Returns the full path for a request
	 */
	public function getPath() {
		return '/' . trim($this->getPathInfo(), '/');
	}

	/** 
	 * Determines whether a request matches a particular pattern
	 */
	public function matches($pattern) {
		return Loader::helper('text')->match($pattern, $this->getPath());
	}

	/** 
	* If no arguments are passed, returns the post array. If a key is passed, it returns the value as it exists in the post array.
	* If a default value is provided and the key does not exist in the POST array, the default value is returned
	* @param string $key
	* @param mixed $defaultValue
	* @return mixed $value
	 */
	public function post($key = null, $defaultValue = null) {
		if ($key == null) {
			return $_POST;
		}
	  if(isset($_POST[$key])){
			return (is_string($_POST[$key])) ? trim($_POST[$key]) : $_POST[$key];
		}
		return $defaultValue;
	}

	/** 
	* If no arguments are passed, returns the GET array. If a key is passed, it returns the value as it exists in the GET array.
	* Also checks the set array, because this function used to return the value of the $this->set() function
	* If a default value is provided and the key does not exist in the GET array, the default value is returned
	* @param string $key
	* @param mixed $defaultValue
	* @return mixed $value
	*/
	public function get($key = null, $defaultValue = null) {
		if ($key == null) {
			return $_GET;
		}
		if(isset($_GET[$key])){
			return (is_string($_GET[$key])) ? trim($_GET[$key]) : $_GET[$key];
		}
		return $defaultValue;
	}

	public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}


}