<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemOptimizationController extends DashboardBaseController{
	/**
	* Dashboard view - automatically redirects to a default
	* page in the category
	*
	* @return void
	*/
	public function view() {
		$this->redirect('/dashboard/system/optimization/cache');
	}
}
?>