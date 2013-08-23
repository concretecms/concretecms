<?
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
class Concrete5_Library_Controller {

	public $theme = null;
	/*
	 * an array of items set by the set() method.
	 * @var array
	*/ 
	private $sets = array();
	
	/*
	 * array of helper objects
	 * @var array
	*/
	protected $helperObjects = array();
	
	/*
	 * Page Object for the controller's page
	 * @var Page 
	 * 
	*/
	protected $c;
	
	/*
	 * Task to be run ex: 'view'
	 * @var string
	*/
	protected $task = false;
	
	/*
	 * @var array
	*/
	protected $parameters = false;
	
	/*
	 * If the page supports full page caching or not
	 * @var bool
	*/
	protected $supportsPageCache = false;	
	
	/**
	 * array of method names that can't be called through the url
	 * @var array
	*/
	protected $restrictedMethods = array();
	
	
	public function __construct() {
		if (!isset($this->helpers)) {
			$this->helpers[] = 'html';
		}
		foreach($this->helpers as $h) {
			$this->helperObjects[(str_replace('/','_',$h))] = Loader::helper($h);
		}		
	}	
	
	public function __sleep() {
		$this->helperObjects = array();
		return get_object_vars($this);
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
	 * 3. Pass a method that's defined in the base class
	 * 4. Any non-public method
	 * @return void
	*/
	private function setupRequestTask() {
		
		$req = Request::get();
		
		// we are already on the right page now
		// let's grab the right method as well.
		$task = substr('/' . $req->getRequestPath(), strlen($req->getRequestCollectionPath()) + 1);

		// remove legacy separaters
		$task = preg_replace('/^\-\//', '', $task);

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
		
		$foundTask = false;
		
		try {
			$r = new ReflectionMethod(get_class($this), $method);
			$cl = $r->getDeclaringClass();
			if (is_object($cl)) {
				if (
					!($cl->getName() == 'Controller' || $cl->getName() == 'Concrete5_Library_Controller') 
					&& strpos($method, 'on_') !== 0 
					&& strpos($method, '__') !== 0 
					&& $r->isPublic()
					&& (is_array($this->restrictedMethods) && !in_array($cl->getName(), $this->restrictedMethods))
					) {
					$foundTask = true;
				}
			}
		} catch(Exception $e) {
		
		}
			
		if ($foundTask) {

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
			if (get_class($this) != 'PageNotFoundController') { 
				if (!is_object($this->c)) {
					// this means we're calling the render directly, so we never 404
					$do404 = false;
				} else if (!is_callable(array($this, $this->task)) && count($this->parameters) > 0) {
					$do404 = true;
				} else if (is_callable(array($this, $this->task))  && (get_class($this) != 'PageForbiddenController')) {
					// we use reflection to see if the task itself, which now much exist, takes fewer arguments than 
					// what is specified
					$r = new ReflectionMethod(get_class($this), $this->task);
					if ($r->getNumberOfParameters() < count($this->parameters)) {
						$do404 = true;
					}
				}
			}
			
			if ($req->isIncludeRequest()) {
				$do404 = false;
			}
		
			if($do404==true) {
			    if (in_array('__call', get_class_methods(get_class($this)))) {
			        $this->task = $method;
			        if (!is_array($this->parameters)) {
			            $this->parameters = array();
			            if (isset($taskparts[1])) {
			                array_shift($taskparts);
			                $this->parameters = $taskparts;
			            }
			        }
			        $do404 = false;
			    }
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
				$cont->view();
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
	 * @param string | array $method
	 * @param array $params 
	 * @return void
	 */
	public function runTask($method, $params) {
		// can be an array of cyclable methods. The first one found is fired.
		if (is_array($method)) {
			$methodArray = $method;
		} else {
			$methodArray[] = $method;
		}
		foreach($methodArray as $method) {
			if (is_callable(array($this, $method))) {
				if(!is_array($params)) {
					$params = array($params);
				}
				return call_user_func_array(array($this, $method), $params);
			}
		}
		return null;
	}

	/**
	 * Determines if a given method is able to be called
	 * @depricated doesn't appear to be used.
	 * @param string $method
	 * @return bool 
	*/ 
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
	 * @depricated
	 */
	/*
	// no longer used. we use reflection
	public function setupRestrictedMethods() {
		$methods = get_class_methods('Controller');
		$this->restrictedMethods = $methods;
	}
	*/
	
	/** 
	 * Returns true if the current request is a POST requested
	 * @return bool
	 */
	public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
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
		if (isset($this->sets[$key])) {
			return $this->sets[$key];
		}
		if(isset($_GET[$key])){
			return (is_string($_GET[$key])) ? trim($_GET[$key]) : $_GET[$key];
		}
		return $defaultValue;
	}
	
	
	/** 
	* If no arguments are passed, returns the REQUEST array. If a key is passed, it returns the value as it exists in the request array.
	* If a default value is provided and the key does not exist in the REQUEST array, the default value is returned
	* @param string $key
	* @param mixed $defaultValue
	* @return mixed $value
	*/
	public function request($key = null, $defaultValue = null) {
		if ($key == null) {
			return $_REQUEST;
		}
		if(isset($_REQUEST[$key])){
			return (is_string($_REQUEST[$key])) ? trim($_REQUEST[$key]) : $_REQUEST[$key];
		}
		return $defaultValue;
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
	 * Adds an item to the view's footer. This item will then be automatically printed out before the </body> section of the page
	 * @param string $item
	 * @return void
	 */
	public function addFooterItem($item) { 
		$v = View::getInstance();
		$v->addFooterItem($item, 'CONTROLLER');
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
		$url = Loader::helper('security')->sanitizeURL($url);
		if($this->isValidExternalUrl($url)){
			if($http_status){
				header($http_status);
			}
			header('Location: '.$url);		
			exit;
		}
		throw new Exception(t('Invalid Redirect URL'));
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
	 * Whether a particular single page controller supports full page caching
	 * @return bool
	 */
	public function supportsPageCache() {
		return $this->supportsPageCache;
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
			// this $view used to be $method which doesn't exist
			$this->on_before_render($view);
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
	 * Gets the array of parameters passed to the controller
	 * @return array
	 */
	public function getControllerParameters() { 
		if (is_array($this->parameters)) {
			return $this->parameters;
		}
		return array();
	}
	
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
  
	/** 
	 * Outputs a list of items set by the addFooterItem() function
	 * @return void
	 */
	public function outputFooterItems() {
		$v = View::getInstance();
		$v->outputFooterItems();
	}
}