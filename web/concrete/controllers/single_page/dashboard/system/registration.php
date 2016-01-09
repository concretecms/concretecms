<?php
namespace Concrete\Controller\SinglePage\Dashboard\System;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Registration extends DashboardPageController {

	public function view(){
		$this->redirect('/dashboard/system/registration/open/');
	}
}