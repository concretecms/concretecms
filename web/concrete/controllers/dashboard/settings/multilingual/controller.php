<?

defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/dashboard/base');

class DashboardSettingsMultilingualController extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function view() {
		Loader::library('3rdparty/Zend/Locale');
		$languages = Localization::getAvailableInterfaceLanguages();
		if (count($languages) > 0) { 
			array_unshift($languages, 'en_US');
		}
		$locales = array();
		foreach($languages as $lang) {
			$loc = new Zend_Locale($lang);
			$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
		}
		$this->set('LANGUAGE_CHOOSE_ON_LOGIN', Config::get('LANGUAGE_CHOOSE_ON_LOGIN'));
		$this->set('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED', Config::get('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED'));
		$this->set('interfacelocales', $locales);
		$this->set('languages', $languages);
		
		// get all locales for multilingual content
		if (Config::get('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED')) {
			$locales = array();
			$languages = Localization::getAvailableContentLanguages();
			array_unshift($languages, 'en');
			$countries = Zend_Locale::getTranslationList('territory', ACTIVE_LOCALE);
			$locales[''] = t('** Choose a Language');
			foreach($languages as $lang) {
				$loc = new Zend_Locale($lang);
				$language = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
				$locales[$lang] = $language;
				if ($loc->getRegion() != '') {
					$locales[$lang] .= ' (' . $countries[$loc->getRegion()] . ')';
				}
			}
			
			$this->set('pages', LanguageSectionPage::getList());			
			$this->set('locales', $locales);
		}
	}
	
	public function load_icons() {
		if (!$this->post('lsLanguage')) {
			return false;
		}
		$ch = Loader::helper('concrete/interface');
		Loader::library('3rdparty/Zend/Locale');
		// here's what we do. We load all locales, then we filter through all those that match the posted language code
		// and we return html for all regions in that language
		$locales = Zend_Locale::getLocaleList();
		$countries = array();
		$html = '<ul class="ccm-multilingual-choose-flag">';
		
		foreach($locales as $locale => $none) {
			$zl = new Zend_Locale($locale);
			if ($zl->getLanguage() == $this->post('lsLanguage') || $zl->toString() == $this->post('lsLanguage')) {
				$countries[$zl->getRegion()] = Zend_Locale::getTranslation($zl->getRegion(), 'country', ACTIVE_LOCALE);
			}
		}

		asort($countries);
		$i = 1;
		foreach($countries as $region => $value) {
			$flag = $ch->getFlagIconSRC($region);
			if ($flag) {
				$checked = "";
				if ($this->post('selectedLanguageIcon') == $region) {
					$checked = "checked=\"checked\"";
				} else if ($i == 1 && (!$this->post('selectedLanguageIcon'))) {
					$checked = "checked=\"checked\"";
				}
					
				$html .= '<li><input type="radio" name="lsIcon" ' . $checked . ' id="languageIcon' . $i . '" value="' . $region . '" /><label for="languageIcon' . $i . '">' . $flag . ' ' . $value  . '</label></li>';
				$i++;
			}
		}
	
		$html .= '</ul>';
		print $html;
		exit;
	}
	
	public function interface_settings_saved() {
		$this->set('message', t('Interface settings saved'));
		$this->view();
	}

	public function multilingual_content_enabled() {
		$this->set('message', t('Multilingual content enabled'));
		$this->view();
	}

	public function multilingual_content_updated() {
		$this->set('message', t('Multilingual content updated'));
		$this->view();
	}
	
	public function add_content_section() {
		if (Loader::helper('validation/token')->validate('add_content_section')) {
			if ((!Loader::helper('validation/numbers')->integer($this->post('pageID'))) || $this->post('pageID') < 1) {
				$this->error->add(t('You must specify a page for this multilingual content section.'));
			} else {
				$pc = Page::getByID($this->post('pageID'));
				if ($pc->isError() || $pc->getCollectionParentID() != 1) {
					$this->error->add(t('Invalid Page. You must specify a page directly under the home page.'));
				}
			}
			if (!$this->post('lsIcon')) {
				$this->error->add(t('You must choose an icon.'));
			}
			
			if (!$this->error->has()) {
				$lc = LanguageSectionPage::getByID($this->post('pageID'));
				if (is_object($lc)) {
					$this->error->add(t('A language section page at this location already exists.'));
				}
			}
			if (!$this->error->has()) {
				LanguageSectionPage::assign($pc, $this->post('lsLanguage'), $this->post('lsIcon'));
				$this->redirect('/dashboard/settings/multilingual', 'multilingual_content_updated');
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$this->view();
	}
	
	public function enable_multilingual_content() {
		if (Loader::helper('validation/token')->validate('enable_multilingual_content')) {
			
			Config::save('LANGUAGE_MULTILINGUAL_CONTENT_ENABLED', 1);
			$this->redirect('/dashboard/settings/multilingual', 'multilingual_content_enabled');
			
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
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