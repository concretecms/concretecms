<?php

namespace Concrete\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Conversations extends DashboardPageController {

	public function view() {
		$this->redirect('/dashboard/conversations/messages');
	}

}