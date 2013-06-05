<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Mail extends Controller {
	protected $sendUndefinedTasksToView = false;
	

	public function view() {
		$this->redirect('/dashboard/system/mail/method');
	}
			
}