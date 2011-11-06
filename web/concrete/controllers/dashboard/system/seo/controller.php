<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemSeoController extends DashboardBaseController{
	/**
	* Dashboard view - automatically redirects to a default
	* page in the category
	*
	* @return void
	*/
	public function view() {
		$this->redirect('/dashboard/system/seo/urls');
	}
}
?>