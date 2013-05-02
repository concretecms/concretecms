<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Conversations extends DashboardBaseController {

	public function view() {
		$this->redirect('/dashboard/conversations/messages');
	}

}