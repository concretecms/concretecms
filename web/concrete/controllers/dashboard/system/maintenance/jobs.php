<?php defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemMaintenanceJobsController extends DashboardBaseController {
	function view() {
		Loader::model("job"); 
		Job::installByHandle('index_search');
		$this->set('availableJobs', Job::getAvailableList(0)); 
		$this->set('jobList', Job::getList()); 
		$this->set('auth', Job::generateAuth());
	}
	
	function execute($job) {
		
	}
}