<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_Users extends DashboardPageController {


	public function __construct() { 
		$this->redirect('/dashboard/users/search');	
	}
	
}