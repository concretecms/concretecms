<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Maintenance extends DashboardPageController {

	public $helpers = array('form');

	public function view() {
		if ($this->isPost()) {
			if ($this->token->validate("update_maintenance")) {
				$mode = $this->post('site_maintenance_mode');
				if ($mode == 1) {
					Config::save('concrete.maintenance_mode', true);
					$this->redirect('/dashboard/system/permissions/maintenance','saved',"enabled");
					exit;
				} else {
					Config::save('concrete.maintenance_mode', false);
					$this->redirect('/dashboard/system/permissions/maintenance','saved',"disabled");
					exit;
				}
			} else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
		$site_maintenance_mode = Config::get('concrete.maintenance_mode');
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
