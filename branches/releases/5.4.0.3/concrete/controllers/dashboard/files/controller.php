<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardFilesController extends Controller {

	public function view() {
		$this->redirect('/dashboard/files/search');
	}

	
}

?>