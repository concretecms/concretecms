<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemController extends Controller { 	 
	
	function view() {  
		$this->redirect('/dashboard/system/jobs');
	}
	
}
