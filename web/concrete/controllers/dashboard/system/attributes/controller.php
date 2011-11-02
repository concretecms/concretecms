<?php defined('C5_EXECUTE') or die('Access Denied');

class DashboardSystemAttributesController extends DashboardBaseController {
	
	public function view() {
		$this->redirect("/dashboard/system/attributes/types");
	}
}