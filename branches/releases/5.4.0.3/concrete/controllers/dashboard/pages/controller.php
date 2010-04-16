<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardPagesController extends Controller {

	public function view() {
		$this->redirect('/dashboard/pages/themes');
	}

	
}

?>