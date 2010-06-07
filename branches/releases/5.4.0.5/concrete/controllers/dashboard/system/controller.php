<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardSystemController extends Controller { 	 
	
	function view() {  
		$this->redirect('/dashboard/system/jobs');
	}
	
}
