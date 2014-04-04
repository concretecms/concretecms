<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Account_Profile extends AccountPageController {

	public function view() {
		$this->redirect('/account/profile/public');
	}
		
}