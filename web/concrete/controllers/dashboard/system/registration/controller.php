<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/dashboard/base');

class DashboardSystemRegistrationController extends DashboardBaseController {

	public function view(){
		$this->redirect('/dashboard/system/registration/public_registration/');
	}
}
?>