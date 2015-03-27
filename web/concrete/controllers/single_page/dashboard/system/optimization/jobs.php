<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Job;
use JobSet;

class Jobs extends DashboardPageController {

	function on_start() 
	{
		parent::on_start();
		// clear the environment overrides cache first
		$env = \Environment::get();
		$env->clearOverrideCache();

		$installed = Job::getList();
		$this->set('availableJobs', Job::getAvailableList(0)); 
		$this->set('installedJobs', $installed); 
		$this->set('jobSets', JobSet::getList());
		$this->set('auth', Job::generateAuth());
	}

	public function view() 
	{
		$this->set('jobListSelected', true);
	}
	
	public function view_sets() 
	{
		$this->set('jobSetsSelected', true);
	}

	function install($handle = null) 
	{
		if ($handle) 
		{
			Job::installByHandle($handle);
			$this->redirect('/dashboard/system/optimization/jobs', 'job_installed');
		} else {
			$this->error->add(t('No job specified.'));
		}
		$this->view();
	}
	
	function uninstall($job_id = null) 
	{
		if ($job_id) 
		{	
			$job = Job::getByID((int) $job_id);
			if ($job) 
			{
				if (!$job->jNotUninstallable) 
				{
					$job->uninstall();
					$this->redirect('/dashboard/system/optimization/jobs', 'job_uninstalled');
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

	public function job_uninstalled() 
	{
		$this->set('message', t('Job successfully uninstalled.'));
		$this->view();
	}

	public function job_installed() 
	{
		$this->set('message', t('Job successfully installed.'));
		$this->view();
	}
	
	public function reset() 
	{
		$jobs = Job::getList();
		foreach($jobs as $j) 
		{
			$j->reset();
		}
		$this->redirect('/dashboard/system/optimization/jobs', 'reset_complete');
	}

	public function reset_complete() 
	{
		$this->set('message', t('All running jobs have been reset.'));
		$this->view();
	}

	public function set_added() 
	{
		$this->set('success', t('Job set added.'));
		$this->set('jobSetsSelected', true);
	}

	public function edit_set($jsID = false) 
	{
		$this->set('jobSetsSelected', true);
		$js = JobSet::getByID($jsID);
		if (is_object($js)) {
			$this->set('set', $js);
		} else {
			$this->redirect('/dashboard/system/optimization/jobs');
		}
	}

	public function update_set_jobs() 
	{
		if ($this->token->validate('update_set_jobs')) 
		{ 
			$js = JobSet::getByID($this->post('jsID'));
			if (!is_object($js)) 
			{
				$this->error->add(t('Invalid Job set.'));
			}

			if (!$this->error->has()) 
			{
				// go through and add all the attributes that aren't in another set
				$js->clearJobs();
				if (is_array($this->post('jID'))) 
				{
					foreach($_POST['jID'] as $jID) 
					{
						$j = Job::getByID($jID);
						if(is_object($j)) 
						{
							$js->addJob($j);
						}
					}					
				}
				$this->redirect('/dashboard/system/optimization/jobs', 'set_updated');
			}	
			
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
		$this->edit($this->post('asID'));
	}
	
	public function set_updated() 
	{
		$this->set('jobSetsSelected', true);
		$this->set('success', t('Job Set updated successfully.'));
	}

	public function update_set() 
	{
		$this->set('jobSetsSelected', true);
		if ($this->token->validate('update_set')) 
		{ 
			$js = JobSet::getByID($this->post('jsID'));
			if (!is_object($js)) 
			{
				$this->error->add(t('Invalid Job set.'));
			} else {
				if (!trim($this->post('jsName'))) 
				{ 
					$this->error->add(t("Specify a name for your Job set."));
				}
			}
			
			if (!$this->error->has()) 
			{
				$js->updateJobSetName($this->post('jsName'));
				$this->redirect('/dashboard/system/optimization/jobs', 'set_updated');
			}
			
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

	public function set_deleted() 
	{
		$this->set('jobSetsSelected', true);
		$this->set('success', t('Group set deleted successfully.'));
	}

	public function delete_set() 
	{
		$this->set('jobSetsSelected', true);
		if ($this->token->validate('delete_set')) 
		{ 
			$js = JobSet::getByID($this->post('jsID'));
			if (!$js->canDelete()) 
			{
				$this->error->add(t('You cannot delete the default Job set.'));
			}

			if (!is_object($js)) 
			{
				$this->error->add(t('Invalid Job set.'));
			}
			
			if (!$this->error->has()) 
			{
				$js->delete();
				$this->redirect('/dashboard/system/optimization/jobs', 'set_deleted');
			}			
			$this->edit_set($this->post('jsID'));
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

	public function add_set() 
	{
		$this->set('jobSetsSelected', true);
		if ($this->token->validate('add_set')) { 
			if (!trim($this->post('jsName'))) 
			{ 
				$this->error->add(t("Specify a name for your Job set."));
			}
			
			if (!$this->error->has()) 
			{			
				$js = JobSet::add($this->post('jsName'));
				if (is_array($_POST['jID'])) 
				{
					foreach($_POST['jID'] as $jID) 
					{
						$j = Job::getByID($jID);
						if(is_object($j)) 
						{
							$js->addJob($j);
						}
					}					
				}
				$this->redirect('/dashboard/system/optimization/jobs', 'set_added');
			}
			
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

	public function update_job_schedule() 
	{
		$jID = $this->request->request->get('jID');
		$J = Job::getByID($jID);
		$J->setSchedule($this->post('isScheduled'), $this->post('unit'), max(0,(int)$this->post('value')));
		$this->redirect('/dashboard/system/optimization/jobs', 'job_scheduled');
	}
	
	public function job_scheduled() 
	{
		$this->set('success', t('Job schedule updated successfully.'));
		$this->view();
	}
	
	
	public function update_set_schedule() 
	{
		$jsID = $this->post('jsID');
		$S = JobSet::getByID($jsID);
		$S->setSchedule($this->post('isScheduled'), $this->post('unit'), $this->post('value'));
		
		$this->redirect('/dashboard/system/optimization/jobs', 'set_scheduled');
	}
	
	public function set_scheduled() 
	{
		$this->set('success', t('Job Set schedule updated successfully.'));
		$this->view();
	}
}