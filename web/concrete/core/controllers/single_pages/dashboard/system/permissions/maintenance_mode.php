<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Permissions_MaintenanceMode extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function view() {
		if ($this->isPost()) {
			if ($this->token->validate("update_maintenance")) {
				$mode = $this->post('site_maintenance_mode');
				if ($mode == 1) { 
					Config::save('SITE_MAINTENANCE_MODE', 1);
					$this->redirect('/dashboard/system/permissions/maintenance_mode','saved',"enabled");
					exit;
				} else {
					Config::save('SITE_MAINTENANCE_MODE', 0);
					$this->redirect('/dashboard/system/permissions/maintenance_mode','saved',"disabled");
					exit;
				}
			} else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
		$site_maintenance_mode = Config::get('SITE_MAINTENANCE_MODE');
		if ($site_maintenance_mode != 1) {
			$site_maintenance_mode = 0;
		}
		$this->set('site_maintenance_mode', $site_maintenance_mode);
	}
	
	public function saved($s = false) {
		if($s == 'enabled') {
			$this->set('message', t('Maintenance Mode turned on. Your site is now private.'));
		} else {
			$this->set('message', t('Maintenance Mode turned off. Your site is public.'));
		}
		$this->view();
	}

}