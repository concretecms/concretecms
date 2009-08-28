<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::block('form');

class DashboardReportsController extends Controller {

	public function __construct() {
		$this->redirect("/dashboard/reports/forms");
	}

}

?>