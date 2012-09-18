<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Account_Profile extends AccountController {

	public function view() {
		$this->redirect('/account/profile/public');
	}
		
}