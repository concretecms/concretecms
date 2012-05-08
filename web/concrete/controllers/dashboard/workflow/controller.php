<?php
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardWorkflowController extends DashboardBaseController {
	
	public function view() {
		$this->redirect('/dashboard/workflow/list');
	}
	
}