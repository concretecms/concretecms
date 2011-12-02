<?php

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationAntispamHelper {
	
	protected $controller = false;
	
	public function __construct() {
		Loader::model('system/antispam/library');
		$library = SystemAntispamLibrary::getActive();
		if (is_object($library)) { 
			$this->controller = $library->getController();
		}
	}		
	
	public function check($content, $additionalArgs) {
		if ($this->controller) { 
			$args['ip_address'] = $_SERVER['REMOTE_ADDR'];
			$args['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$args['content'] = $content;
			foreach($additionalArgs as $key => $value) {
				$args[$key] = $value;
			}
			return $this->controller->check($args);
		} else {
			return true; // return true if it passes the test
		}
	}
	
	public function __call($nm, $args) {
		if (method_exists($this->controller, $nm)) { 
			return call_user_func_array(array($this->controller, $nm), $args);
		}
	}
	
	
}