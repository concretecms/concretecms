<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Composer extends DashboardController {

	public function view() {
		$this->redirect('/dashboard/composer/write');
	}

	
}