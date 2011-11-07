<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemMaintenanceController extends DashboardBaseController{
	/**
	* Dashboard view - automatically redirects to a default
	* page in the category
	*
	* @return void
	*/
	public function view() {
		$this->redirect('/dashboard/system/maintenance/cache');
	}
}
?>