<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use \Concrete\Core\Page\Controller\DashboardPageController;
use TaskPermission;
use Config;
use Loader;
use View;

class Connect extends DashboardPageController {

	public $helpers = array('form');

	public function on_start() {
		$this->addFooterItem(Loader::helper('html')->javascript('jquery-postmessage.js'));
	}

	public function view($startStep = 'view') {
		$this->set('startStep', $startStep);
	}

	public function connect_complete() {
		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) {
			if (!$_POST['csToken']) {
				$this->set('error', array(t('An unexpected error occurred when connecting your site to the marketplace.')));
			} else {
                Config::save('concrete.marketplace.token', $_POST['csToken']);
                Config::save('concrete.marketplace.url_token', $_POST['csURLToken']);
				print '<script type="text/javascript">parent.window.location.href=\'' . View::url('/dashboard/extend/connect', 'community_connect_success') . '\';</script>';
				exit;
			}
		} else {
			$this->set('error', array(t('You do not have permission to connect this site to the marketplace.')));
		}
	}

	public function community_connect_success() {
		$this->set('message', t('Your site is now connected to the concrete5 community.'));
	}


}
