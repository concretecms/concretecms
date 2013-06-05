<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics_Interface extends DashboardBaseController {

	public function view() {
		$this->set('DASHBOARD_BACKGROUND_IMAGE', Config::get('DASHBOARD_BACKGROUND_IMAGE'));
		$imageObject = false;
		if ($this->get('DASHBOARD_BACKGROUND_IMAGE') == 'custom') { 
			$fID = Config::get('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID');
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
				if (!defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && !defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC')) {
					Config::save('DASHBOARD_BACKGROUND_IMAGE', $this->post('DASHBOARD_BACKGROUND_IMAGE'));
					Config::save('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID', $this->post('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID'));
				}
				$this->redirect('/dashboard/system/basics/interface', 'settings_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}


}