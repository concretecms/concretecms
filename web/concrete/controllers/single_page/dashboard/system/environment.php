<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Environment extends DashboardPageController {

	public function view() {
		$this->redirect('/dashboard/system/environment/info');
	}
}