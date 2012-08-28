<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/login');

class Concrete5_Controller_PageForbidden extends LoginController {
	
	public function view() {
		$v = View::getInstance();
		$c = $v->getCollectionObject();
		if (is_object($c)) {
			$cID = $c->getCollectionID();
			if($cID) { 
				$this->forward($cID); // set the intended url
			}
		}
		parent::view();
		$u = new User();
		$logged = $u->isLoggedIn();
		if(!$logged && FORBIDDEN_SHOW_LOGIN) { //if they are not logged in, and we show guests the login...
			$this->render('/login');
		}
	}
	
}