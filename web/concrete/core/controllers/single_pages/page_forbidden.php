<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/login');

class Concrete5_Controller_PageForbidden extends LoginController {
	
	public function view() {
		$c = Page::getCurrentPage();
		if (is_object($c)) {
			$cID = $c->getCollectionID();
			if($cID) { 
				$this->forward($cID); // set the intended url
			}
		}
		parent::view();
		$u = new User();
		if(!$u->isRegistered() && FORBIDDEN_SHOW_LOGIN) { //if they are not logged in, and we show guests the login...
			$this->render('/login');
		}
	}
	
}