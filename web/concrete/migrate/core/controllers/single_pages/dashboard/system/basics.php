<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_System_Basics extends DashboardController {

	public function view() {
		$this->redirect('/dashboard/system/basics/site_name');
	}


}