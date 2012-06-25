<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics extends Controller {

	public function view() {
		$this->redirect('/dashboard/system/basics/site_name');
	}


}