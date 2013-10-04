<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics_Multilingual extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function view() {
		$locales = Localization::getAvailableInterfaceLanguageDescriptions();
		$this->set('LANGUAGE_CHOOSE_ON_LOGIN', Config::get('LANGUAGE_CHOOSE_ON_LOGIN'));
		$this->set('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED', Config::get('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED'));
		$this->set('interfacelocales', $locales);
	}
	
	public function on_start() {
		$this->token = Loader::helper('validation/token');
	}

	public function interface_settings_saved() {
		$this->set('message', t('Interface settings saved. Please log out and in again to update all backend messages.'));
		$this->view();
	}
	public function save_interface_language() {
		if (Loader::helper('validation/token')->validate('save_interface_language')) {
			
			if($this->post('SITE_LOCALE')){
				Config::save('SITE_LOCALE', $this->post('SITE_LOCALE'));
			}
			Config::save('LANGUAGE_CHOOSE_ON_LOGIN', $this->post('LANGUAGE_CHOOSE_ON_LOGIN'));
			$this->redirect('/dashboard/system/basics/multilingual', 'interface_settings_saved');
	
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}
	
}