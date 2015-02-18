<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Localization;

class Multilingual extends DashboardPageController {

	public $helpers = array('form');

	public function view() {
		$locales = Localization::getAvailableInterfaceLanguageDescriptions();
		$this->set('LANGUAGE_CHOOSE_ON_LOGIN', Config::get('concrete.i18n.choose_language_login'));
		$this->set('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED', Config::get('concrete.i18n.enabled'));
		$this->set('interfacelocales', $locales);
		$this->set('SITE_LOCALE', Config::get('concrete.locale'));
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
				Config::save('concrete.locale', $this->post('SITE_LOCALE'));
			}
			Config::save('concrete.i18n.choose_language_login', $this->post('LANGUAGE_CHOOSE_ON_LOGIN'));
			$this->redirect('/dashboard/system/basics/multilingual', 'interface_settings_saved');

		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}

}
