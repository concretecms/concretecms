<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardScrapbookController extends Controller {

	public function view() { 
		$this->redirect('/dashboard/scrapbook/user');
	}
	
}

?>