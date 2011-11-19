<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemEnvironmentController extends DashboardBaseController {

	public function view() {
		$this->redirect('/dashboard/system/environment/info');
	}
}