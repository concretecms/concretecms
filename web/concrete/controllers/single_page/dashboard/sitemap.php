<?php
namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
class Sitemap extends DashboardPageController {

	public function view() {
		$this->redirect('/dashboard/sitemap/full');
	}
	
}