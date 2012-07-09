<?php

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Validation_Captcha {
	
	public function __construct() {
		Loader::model('system/captcha/library');
		Loader::model('system/captcha/controller');
		$captcha = SystemCaptchaLibrary::getActive();
		$this->controller = $captcha->getController();
	}		
	
	public function __call($nm, $args) {
		if (method_exists($this->controller, $nm)) { 
			return call_user_func_array(array($this->controller, $nm), $args);
		}
	}

	
}