<?
namespace Concrete\Helper\Validation;
class Captcha {
	
	public function __construct() {
		Loader::model('system/captcha/library');
		Loader::model('system/captcha/controller');
		$captcha = \Concrete\Core\Captcha\Library::getActive();
		$this->controller = $captcha->getController();
	}		
	
	public function __call($nm, $args) {
		if (method_exists($this->controller, $nm)) { 
			return call_user_func_array(array($this->controller, $nm), $args);
		}
	}

	
}