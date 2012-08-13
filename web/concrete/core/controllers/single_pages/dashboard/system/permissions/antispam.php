<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model("system/antispam/library");

class Concrete5_Controller_Dashboard_System_Permissions_Antispam extends DashboardBaseController {
	
	public function view() {
		$list = SystemAntispamLibrary::getList();
		$libraries = array('' => t('** None Activated'));
		foreach($list as $sc) {
			$libraries[$sc->getSystemAntispamLibraryHandle()] = $sc->getSystemAntispamLibraryName();
		}
		$scl = SystemAntispamLibrary::getActive();
		$this->set('activeLibrary', $scl);
		$this->set('libraries', $libraries);
	}
	
	public function saved() {
		$this->set('message', t('Anti-spam settings saved.'));
		$this->view();
	}
	
	public function update_library() {
		if (Loader::helper("validation/token")->validate('update_library')) {
			if ($this->post('activeLibrary')) { 
				$scl = SystemAntispamLibrary::getByHandle($this->post('activeLibrary'));
				if (is_object($scl)) {
					$scl->activate();
					Config::save('ANTISPAM_NOTIFY_EMAIL', $this->post('ANTISPAM_NOTIFY_EMAIL'));
					Config::save('ANTISPAM_LOG_SPAM', $this->post('ANTISPAM_LOG_SPAM'));
					if ($scl->hasOptionsForm() && $this->post('ccm-submit-submit')) {
						$controller = $scl->getController();
						$controller->saveOptions($this->post());
					}
					$this->redirect('/dashboard/system/permissions/antispam', 'saved');
				} else {
					$this->error->add(t('Invalid anti-spam library.'));
				}
			} else {
				SystemAntispamLibrary::deactivateAll();
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}
}