<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemJobsController extends Controller { 	 
	
	function view(){  
		$jobObj = Loader::model("job"); 
		Job::installByHandle('index_search');
		$this->set( 'availableJobs', Job::getAvailableList(0) ); 
		$this->set( 'jobListRS', Job::getList() ); 
		$this->set('auth', Job::generateAuth());
	}
	
	function install(){
		$jobObj = Loader::model("job");
		Job::installByHandle($_POST['jHandle']);
		$this->redirect('/dashboard/system/jobs');
	}
	
	function uninstall(){
		Loader::model("job");
		$jobObj=Job::getByID( intval($_REQUEST['jID']) ); 
		if( $jobObj && !$jobObj->jNotUninstallable ){
			$jobObj->uninstall();
		}		
		$this->redirect('/dashboard/system/jobs');
	}
}

?>