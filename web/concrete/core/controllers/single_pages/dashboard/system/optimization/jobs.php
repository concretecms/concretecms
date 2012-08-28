<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Optimization_Jobs extends DashboardBaseController {
	function view() {
		Job::installByHandle('index_search');
		$this->set('availableJobs', Job::getAvailableList(0)); 
		$this->set('jobList', Job::getList()); 
		$this->set('auth', Job::generateAuth());
	}
	
	function install($handle = null) {
		if ($handle) {
			Loader::model("job");
			Job::installByHandle($handle);
			$this->set('message', t('Job succesfully installed.'));
		} else {
			$this->error->add(t('No job specified.'));
		}
		$this->view();
	}
	
	function uninstall($job_id = null) {
		if ($job_id) {
			Loader::model("job");
			$job = Job::getByID((int) $job_id);
			if ($job) {
				if (!$job->jNotUninstallable) {
					$job->uninstall();
					$this->set('message', t('Job succesfully uninstalled.'));
				} else {
					$this->error->add(t('This job cannot be uninstalled.'));
				}
			} else {
				$this->error->add(t('Job not found.'));
			}
		} else {
			$this->error->add(t('No job specified.'));
		}
		$this->view();
	}
	
	public function reset_running_jobs() {
		Loader::model('job');
		if (Loader::helper('validation/token')->validate('reset_running_jobs')) { 
			Job::resetRunningJobs();
			$this->redirect('/dashboard/system/optimization/jobs', 'reset_complete');
		}
	}
	public function reset_complete() {
		$this->set('message', t('All running jobs have been reset.'));
		$this->view();
	}
	
}