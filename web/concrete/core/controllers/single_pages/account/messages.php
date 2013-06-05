<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Account_Messages extends Controller {
	
	public function view() {
		$this->redirect('/account/messages/inbox');
	}

}