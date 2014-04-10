<?

defined('C5_EXECUTE') or die("Access Denied.");
Loader::block('form');
class Concrete5_Controller_Page_Dashboard_Reports extends DashboardPageController {

	public function __construct() {
		$this->redirect("/dashboard/reports/statistics");
	}

}