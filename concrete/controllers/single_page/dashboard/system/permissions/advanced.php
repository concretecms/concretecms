<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
class Advanced extends DashboardPageController {

	public function enable_advanced_permissions() {
		if ($this->token->validate("enable_advanced_permissions")) {
			Config::save('concrete.permissions.model', 'advanced');
			$this->redirect('/dashboard/system/permissions/advanced', 'permissions_enabled');
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

	public function permissions_enabled() {
		$this->set('message', t('Advanced permissions enabled.'));
	}

}
