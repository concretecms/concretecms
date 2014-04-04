<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Account_Messages extends PageController {
	
	public function view() {
		$this->redirect('/account/messages/inbox');
	}

}