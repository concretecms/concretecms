<?php
namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Users extends DashboardPageController {


	public function __construct() { 
		$this->redirect('/dashboard/users/search');	
	}
	
}