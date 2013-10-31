<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_PageForbidden extends Controller {
	
	protected $viewPath = '/page_forbidden';

	public function view() {
		$u = new User();
		if (!$u->isRegistered() && FORBIDDEN_SHOW_LOGIN) { //if they are not logged in, and we show guests the login...
			$this->redirect('/login');
		}
	}


}