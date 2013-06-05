<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Permissions_Advanced extends DashboardBaseController {
	
	public function enable_advanced_permissions() {
		if ($this->token->validate("enable_advanced_permissions")) { 
			Config::save('PERMISSIONS_MODEL', 'advanced');
			$this->redirect('/dashboard/system/permissions/advanced', 'permissions_enabled');
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}
	
	public function permissions_enabled() {
		$this->set('message', t('Advanced permissions enabled.'));
	}

}