<?php 

defined('C5_EXECUTE') or die("Access Denied.");


/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A generic object that every block or page controller extends
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Controller {

	public $theme = null;
	// sets is an array of items set by the set() method. Whew.
	private $sets = array();
	protected $helperObjects = array();
	protected $c; // collection
	protected $task = false;
	protected $parameters = false;
	
	
	/**
	 * Items in here CANNOT be called through the URL
	 */
	private $restrictedMethods = array();
	
	public function __construct() {

		if (!isset($this->helpers)) {
			$this->helpers[] = 'html';
		}
		foreach($this->helpers as $h) {
			$$h = Loader::helper($h);
			$this->helperObjects[(str_replace('/','_',$h))] = $$h;
		}
		
	}	

	/**
	 * @access private
	 */
	public function getRenderOverride() {
		return $this->renderOverride;
	}
	
	
	/** 
	 * Is responsible for taking a method passed and ensuring that it is valid for the current request. You can't
	 * 1. Pass a method that starts with "on_"
	 * 2. Pass a method that's in the restrictedMethods array
	 */
	private function setupRequestTask() {
		
		$req = Request::get();
		
		// we are already on the right page now
		// let's grab the right method as well.
		$task = substr($req->getRequestPath(), strlen($req->getRequestCollectionPath()));
		
		// remove legacy separaters
		$task = str_replace('-/', '', $task);
		
		// grab the whole shebang
		$taskparts = explode('/', $task);
		
		if (isset($taskparts[0]) && $taskparts[0] != '') {
			$method = $taskparts[0];
		}

		if ($method == '') {
			if (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
				$method = $this->c->getCollectionHandle();
			} else {
				$method = 'view';
			}
			$this->parameters = array();
			
		}
		
		if (is_callable(array($this, $method)) && (strpos($method, 'on_') !== 0) && (!in_array($method, $this->restrictedMethods))) {
		
			$this->task = $method;
			if (!is_array($this->parameters)) {
				$this->parameters = array();
				if (isset($taskparts[1])) {
					array_shift($taskparts);
					$this->parameters = $taskparts;
				}
			}
		
		} else {

 			$this->task = 'view';
			if (!is_array($this->parameters)) {
				$this->parameters = array();
				if (isset($taskparts[0])) {
					$this->parameters = $taskparts;
				}
			}
			
			// finally we do a 404 check in this instance
			// if the particular controller does NOT have a view method but DOES have arguments passed
			// we call 404
			
			$do404 = false;
			if (!is_object($this->c)) {
				// this means we're calling the render directly, so we never 404
				$do404 = false;
			} else if (!is_callable(array($this, $this->task)) && count($this->parameters) > 0) {
				$do404 = true;
			} else if (is_callable(array($this, $this->task))) {
				// we use reflection to see if the task itself, which now much exist, takes fewer arguments than 
				// what is specified
				$r = new ReflectionMethod(get_class($this), $this->task);
				if ($r->getNumberOfParameters() < count($this->parameters)) {
					$do404 = true;
				}
			}
			
			if ($req->isIncludeRequest()) {
				$do404 = false;
			}
		


			if ($do404) {
				
				// this is hacky, the global part
				global $c;
				$v = View::getInstance();
				$c = new Page();
				$c->loadError(COLLECTION_NOT_FOUND);
				$v->setCollectionObject($c);
				$this->c = $c;
				$cont = Loader::controller("/page_not_found");
				$v->setController($cont);				
				$v->render('/page_not_found');
			}
 		}

 		
 	}
	
	/** 
	 * Based on the current request, the Controller object is loaded with the parameters and task requested
	 * The requested method is then run on the active controller (if that method exists)
	 * @return void
	 */	
	public function setupAndRun() {
		$req = Request::get();
		$this->setupRequestTask();
		$this->on_start();
		
		if ($this->task) {
			$this->runTask($this->task, $this->parameters);
		}
	}
	
	public function on_start() {
	
	}
	
	public function on_before_render() {
	
	}
	
	/** 
	 * Runs a task in the active controller if it exists.
	 * @return void
	 */
	public function runTask($method, $params) {
		if (is_callable(array($this, $method))) {
			if(!is_array($params)) {
				$params = array($params);
			}
			$ret = call_user_func_array(array($this, $method), $params);
		}
		return $ret;
	}

	private function isCallable($method) {
		if (in_array($method, $this->restrictedMethods)) {
			return false;
		}
		
		if (is_callable(array($this, $method))) {
			return true;
		}
	}
	
	/**
	 * @access private
	 */
	public function setupRestrictedMethods() {
		$methods = get_class_methods('Controller');
		$this->restrictedMethods = $methods;
	}
	
	/** 
	 * Returns true if the current request is a POST requested
	 * @return bool
	 */
	public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/** 
	 * If no arguments are passed, returns the post array. If a key is passed, it returns the value as it exists in the post array.
	 * @param string $key
	 * @return string $value
	 */
	public function post($key = null) {
		if ($key == null) {
			return $_POST;
		}
		
		if (isset($_POST[$key])) {
			if (is_string($_POST[$key])) {
				return trim($_POST[$key]);
			} else {
				return $_POST[$key];
			}
		} else {
			return null;
		}
	}
	
	/** 
	 * If no arguments are passed, returns the GET array. If a key is passed, it returns the value as it exists in the GET array.
	 * Also checks the set array, because this function used to return the value of the $this->set() function
	 * @param string $key
	 * @return string $value
	 */
	public function get($key = null) {
		if ($key == null) {
			return $_GET;
		}
		
		if (isset($this->sets[$key])) {
			return $this->sets[$key];
		}
		
		if (isset($_GET[$key])) {
			if (is_string($_GET[$key])) {
				return trim($_GET[$key]);
			} else {
				return $_GET[$key];
			}
		} else {
			return null;
		}
	}
	
	/** 
	 * If no arguments are passed, returns the REQUEST array. If a key is passed, it returns the value as it exists in the request array.
	 * @param string $key
	 * @return string $value
	 */
	public function request($key = null) {
		if ($key == null) {
			return $_REQUEST;
		}
		
		if (isset($_REQUEST[$key])) {
			if (is_string($_REQUEST[$key])) {
				return trim($_REQUEST[$key]);
			} else {
				return $_REQUEST[$key];
			}
		} else {
			return null;
		}
	}
	
	/** 
	 * Sets a variable to be passed through from the controller to the view
	 * @param string $key
	 * @param string $val
	 * @return void
	 */
	public function set($key, $val) {
		$loc = CacheLocal::get();
		$loc->cache['controllerSets'][$key] = $val;
		$this->sets[$key] = $val;
	}
	
	/** 
	 * Returns the value of a previous set()
	 * @param string $key
	 * @return string $value
	 */
	public function getvar($key) {
		return $this->sets[$key];
	}
	
	/** 
	 * Adds an item to the view's header. This item will then be automatically printed out in the <head> section of the page
	 * @param string $item
	 * @return void
	 */
	public function addHeaderItem($item) { 
		$v = View::getInstance();
		$v->addHeaderItem($item, 'CONTROLLER');
	}

	/** 
	 * Redirects to a given URL
	 * @param string $location
	 * @param string $task
	 * @param string $params
	 * @return void
	 */
	public function redirect() {
		$args = func_get_args();
		$url = call_user_func_array(array('View', 'url'), $args);
		if ($url == '') {
			$url = BASE_URL . DIR_REL;
		}
		header("Location: " . $url);
		exit;
	}

	/** 
	 * Redirects to a given external URL
	 * @param string $url
	 * @param string $http_status
	 */	
	public function externalRedirect($url,$http_status=false) {
		if($this->isValidExternalUrl($url)){
			if($http_status){
				header($http_status);
			}
			header('Location: '.$url);		
			exit;
		}
		throw new Exception('Invalid Redirect URL');
	}

	/** 
	 * Validates an external URL request to avoid possible shenanagins
	 *
	 * Placeholder for now
	 * @param string $url
	 * @param string $http_status
	 */	
	public function isValidExternalUrl($url){
		return true;
	}
	
	/** 
	 * Renders a view with the current controller as its controller
	 * @param string $view
	 * @return void
	 */
	public function render($view) {
		$v = View::getInstance();
		$this->renderOverride = $view;
		$c = Page::getCurrentPage();
		$v->setCollectionObject($c);
		$v->setController($this);
		if (method_exists($this, 'on_before_render')) {
			call_user_func_array(array($this, 'on_before_render'), array($method));
		}
		$v->render($view);
	}
	
	/**
	 * Sets the current controller's page object
	 * @param Page $c
	 * @return void
	 */
	public function setCollectionObject($c) {$this->c = $c;}
	
	/** 
	 * Gets the current controller's page object.
	 * @return Page
	 */
	public function getCollectionObject() {return $this->c;}
	
	/** 
	 * Gets the current view for the controller (typically the page's handle)
	 * @return string $view
	 */
	public function getView() {return $this->c->getCollectionHandle();}
	
	/** 
	 * Gets the task requested of the controller
	 * @return string
	 */
	public function getTask() {return $this->task;}
	
	/** 
	 * Gets the array of items that have been set using set()
	 * @return array
	 */
	public function getSets() { 
		$loc = CacheLocal::get();
		if (isset($loc->cache['controllerSets'])) {
			return $loc->cache['controllerSets'];
		}
		return array();
	}
	
	/** 
	 * Gets an array of helper objects that have been set using the $helpers array
	 * @return array
	 */
	public function getHelperObjects() { return $this->helperObjects; }
	
	/** 
	 * Outputs a list of items set by the addHeaderItem() function
	 * @return void
	 */
	public function outputHeaderItems() {
		$v = View::getInstance();
		$v->outputHeaderItems();
	}

}

?>