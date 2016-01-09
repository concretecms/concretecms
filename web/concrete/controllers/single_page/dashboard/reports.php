<?php

namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
class Reports extends DashboardPageController {

	public function __construct() {
		$this->redirect("/dashboard/reports/forms");
	}

}