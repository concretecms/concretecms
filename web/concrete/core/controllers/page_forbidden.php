<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_PageForbidden extends Controller {
	
	protected $viewPath = '/page_forbidden';

	public function view() {
		$u = new User();
		if (!$u->isRegistered() && FORBIDDEN_SHOW_LOGIN) { //if they are not logged in, and we show guests the login...
			Redirect::send('/login');
		}
	}

	/*

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

	*/	

}