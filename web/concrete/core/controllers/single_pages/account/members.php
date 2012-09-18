<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Account_Members extends Controller {
	
	public function view() {
		$this->redirect('/account/members/directory');
	}

}