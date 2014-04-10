<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_System_Permissions extends DashboardPageController {
	/**
	* Dashboard view - automatically redirects to a default
	* page in the category
	*
	* @return void
	*/
	public function view() {
		$this->redirect('/dashboard/system/permissions/site');
	}
}
?>