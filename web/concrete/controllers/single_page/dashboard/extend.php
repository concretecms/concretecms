<?php
namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
class Extend extends DashboardPageController {

	public function view() {
		$this->redirect('/dashboard/extend/install');
	}

	
}