<?

defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_Controller {

	protected $view;
	protected $helperObjects = array();
	protected $sets = array();
	protected $action;
	protected $parameters;

	public function set($key, $val) {
		$this->sets[$key] = $val;
	}

	public function getSets() {
		return $this->sets;
	}



	public function getHelperObjects() {
		$helpers = array();
		foreach($this->helpers as $handle) {
			$h = Loader::helper($handle);
			$helpers[(str_replace('/','_',$handle))] = $h;
		}		
		return $helpers;
	}

	public function get($key = null, $defaultValue = null) {
		if ($key == null) {
			return $_GET;
		}
		if (isset($this->sets[$key])) {
			return $this->sets[$key];
		}
		return Request::get($key, $defaultValue);
	}

	public function getTask() {
		return $this->getAction();
	}

	public function getAction() {
		return $this->action;
	}

	public function runAction($action, $parameters) {
		$this->action = $action;
		$this->parameters = $parameters;
		return call_user_func_array(array($this, $action), $parameters);
	}

	public function getView() {return $this->view;}

	public function on_start() {}
	public function on_before_render() {}

	/** 
	 * @deprecated
	 */
	public function post($key = null, $defaultValue = null) {
		return Request::post($key, $defaultValue);
	}
	public function redirect($url) {
		Redirect::send($url);
	}
	

}