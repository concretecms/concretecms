<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_System_Mail extends DashboardPageController {
	protected $sendUndefinedTasksToView = false;
	

	public function view() {
		$this->redirect('/dashboard/system/mail/method');
	}
			
}