<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_System_Conversations extends DashboardPageController {

	public function view() {
		$this->redirect('/dashboard/system/conversations/editor');
	}
}