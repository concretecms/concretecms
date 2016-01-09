<?php
namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
class Workflow extends DashboardPageController {
	
	public function view() {
		$this->redirect('/dashboard/workflow/me');
	}
	
}