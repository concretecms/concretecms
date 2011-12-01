<?php

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationCaptchaHelper {
	
	public function __construct() {
		Loader::model('system/captcha/library');
		$captcha = SystemCaptchaLibrary::getActive();
		$this->controller = $captcha->getController();
	}		
	
	public function __call($nm, $args) {
		return call_user_func_array(array($this->controller, $nm), $args);
	}

	
}