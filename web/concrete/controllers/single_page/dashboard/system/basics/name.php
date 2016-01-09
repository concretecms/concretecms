<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Name extends DashboardPageController {

	public function view() {
		$this->set('site', h(Config::get('concrete.site')));
	}

	public function sitename_saved() {
		$this->set('message', t("Your site's name has been saved."));
		$this->view();
	}

	public function update_sitename() {
		if ($this->token->validate("update_sitename")) {
			if ($this->isPost()) {
				Config::save('concrete.site', $this->post('SITE'));
				$this->redirect('/dashboard/system/basics/name','sitename_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}


}
