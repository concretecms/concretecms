<?php defined('C5_EXECUTE') or die('Access Denied');

class Concrete5_Controller_Page_Dashboard_System_Attributes extends DashboardPageController {
	
	public function view() {
		$this->redirect("/dashboard/system/attributes/types");
	}
}