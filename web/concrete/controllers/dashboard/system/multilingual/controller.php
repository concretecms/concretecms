<?

defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/dashboard/base');

class DashboardSystemMultilingualController extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function on_start() {
		$subnav = array(
			array(View::url('/dashboard/settings'), t('General')),
			array(View::url('/dashboard/settings/mail'), t('Email')),
			array(View::url('/dashboard/settings', 'set_permissions'), t('Access')),
			array(View::url('/dashboard/settings/multilingual'), t('Multilingual'), true),
			array(View::url('/dashboard/settings', 'set_developer'), t('Debug')),
			array(View::url('/dashboard/settings', 'manage_attribute_types'), t('Attributes'))
		);
		$this->set('subnav', $subnav);	}
	
	public function view() {
		Loader::library('3rdparty/Zend/Locale');
		Loader::library('3rdparty/Zend/Locale/Data');
		$languages = Localization::getAvailableInterfaceLanguages();
		if (count($languages) > 0) { 
			array_unshift($languages, 'en_US');
		}
		$locales = array();
		Zend_Locale_Data::setCache(Cache::getLibrary());
		foreach($languages as $lang) {
			$loc = new Zend_Locale($lang);
			$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
		}
		$this->set('LANGUAGE_CHOOSE_ON_LOGIN', Config::get('LANGUAGE_CHOOSE_ON_LOGIN'));
		$this->set('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED', Config::get('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED'));
		$this->set('interfacelocales', $locales);
		$this->set('languages', $languages);
	}
	


	public function interface_settings_saved() {
		$this->set('message', t('Interface settings saved'));
		$this->view();
	}
	public function save_interface_language() {
		if (Loader::helper('validation/token')->validate('save_interface_language')) {
			
			Config::save('SITE_LOCALE', $this->post('SITE_LOCALE'));
			Config::save('LANGUAGE_CHOOSE_ON_LOGIN', $this->post('LANGUAGE_CHOOSE_ON_LOGIN'));
			$this->redirect('/dashboard/settings/multilingual', 'interface_settings_saved');
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}
	
}