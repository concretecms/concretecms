<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class PageForbiddenController extends Controller {
	
	public $helpers = array('form');
	
	public function view() {
		$this->set('intro_msg', 'You must sign in order to access this page!');
		if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
			$this->set('uNameLabel', 'Email Address');
		} else {
			$this->set('uNameLabel', 'Username');
		}
		$this->render('/login');
	}
	
}