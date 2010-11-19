<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardUsersController extends Controller {


	public function __construct() { 
		$this->redirect('/dashboard/users/search');	
	}
	
}

?>