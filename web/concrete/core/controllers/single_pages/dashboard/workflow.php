<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Workflow extends DashboardBaseController {
	
	public function view() {
		$this->redirect('/dashboard/workflow/list');
	}
	
}