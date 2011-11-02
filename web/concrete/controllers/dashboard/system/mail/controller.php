<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('mail/importer');

class DashboardSystemMailController extends Controller {
	protected $sendUndefinedTasksToView = false;
	
	public function view() {
		$this->redirect('/dashboard/system/mail/method');
	}


			
}

?>