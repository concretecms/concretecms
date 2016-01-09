<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use File;

class Images extends DashboardPageController {

	public function view() {
		$this->set('DASHBOARD_BACKGROUND_IMAGE', Config::get('concrete.misc.dashboard_background_image'));
		$imageObject = false;
		if ($this->get('concrete.misc.dashboard_background_image') == 'custom') {
			$fID = Config::get('concrete.misc.dashboard_background_image_fid');
			if ($fID > 0) {
				$imageObject = File::getByID($fID);
				if (is_object($imageObject) && $imageObject->isError()) {
					unset($imageObject);
				}
			}
		}
		$this->set('imageObject', $imageObject);
	}

	public function settings_saved() {
		$this->set('message', t("concrete5 interface settings saved successfully."));
		$this->view();
	}

	public function save_interface_settings() {
		if ($this->token->validate("save_interface_settings")) {
			if ($this->isPost()) {
                Config::save('concrete.misc.dashboard_background_image', $this->post('DASHBOARD_BACKGROUND_IMAGE'));
                Config::save('concrete.misc.dashboard_background_image_fid', $this->post('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID'));
				$this->redirect('/dashboard/system/basics/images', 'settings_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}


}
