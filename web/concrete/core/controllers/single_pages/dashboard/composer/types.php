<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Types extends DashboardBaseController {

	public function composer_added() {
		$this->set('success', t('Composer added successfully.'));
	}
	
}