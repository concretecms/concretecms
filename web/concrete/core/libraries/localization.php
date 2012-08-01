<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Library_Localization {
	
		public function init() {
			$loc = self::getInstance();
			$loc->getTranslate();
		}
		
		public static function getInstance() {
			static $loc;
			if (!isset($loc)) {			
				$loc = new Localization();
			}
			return $loc;
		}
		
		public static function changeLocale($locale) {
			$loc = self::getInstance();
			$loc->setLocale($locale);
		}
		
		public static function activeLocale() {
			$loc = self::getInstance();
			return $loc->getLocale();
		}

		protected $translate;

		public function __construct() {
			Loader::library('3rdparty/Zend/Date');
			Loader::library('3rdparty/Zend/Translate');
			$this->setLocale(defined('ACTIVE_LOCALE') ? ACTIVE_LOCALE : 'en_US');
			Zend_Date::setOptions(array('format_type' => 'php'));
			$cache = Cache::getLibrary();
			if (is_object($cache)) {
				Zend_Translate::setCache($cache);
				Zend_Date::setOptions(array('cache'=>$cache));
			}
		}
		
		public function setLocale($locale) {
			if ($locale != 'en_US' && is_dir(DIR_BASE . '/languages/' . $locale)) {
				if (!isset($this->translate)) {
					$this->translate = new Zend_Translate('gettext', DIR_BASE . '/languages/' . $locale, $locale);
				} else {
					if (!in_array($locale, $this->translate->getList())) {
						$this->translate->addTranslation(DIR_BASE . '/languages/' . $locale, $locale);
					}
					$this->translate->setLocale($locale);
				}
			}
		}
		
		public function getLocale() {
			return isset($this->translate) ? $this->translate->getLocale() : 'en_US';
		}

		public function getActiveTranslateObject() {
			return $this->translate;
		}

		public function addSiteInterfaceLanguage($language) {
			if (is_object($this->translate)) {
				$this->translate->addTranslation(DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', $language);
			} else {
				Loader::library('3rdparty/Zend/Translate');
				$cache = Cache::getLibrary();
				if (is_object($cache)) {
					Zend_Translate::setCache($cache);
				}
				$this->translate = new Zend_Translate(array('adapter' => 'gettext', 'content' => DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', 'locale' => $language, 'disableNotices' => true));
			}
		}
		
		public static function getTranslate() {
			$loc = self::getInstance();
			return $loc->getActiveTranslateObject();
		}
	
		public static function getAvailableInterfaceLanguages() {
			$languages = array();
			$fh = Loader::helper('file');
			
			if (file_exists(DIR_LANGUAGES)) {
				$contents = $fh->getDirectoryContents(DIR_LANGUAGES);
				foreach($contents as $con) {
					if (is_dir(DIR_LANGUAGES . '/' . $con) && file_exists(DIR_LANGUAGES . '/' . $con . '/LC_MESSAGES/messages.mo')) {
						$languages[] = $con;					
					}
				}
			}
			if (file_exists(DIR_LANGUAGES_CORE)) {
				$contents = $fh->getDirectoryContents(DIR_LANGUAGES_CORE);
				foreach($contents as $con) {
					if (is_dir(DIR_LANGUAGES_CORE . '/' . $con) && file_exists(DIR_LANGUAGES_CORE . '/' . $con . '/LC_MESSAGES/messages.mo') && (!in_array($con, $languages))) {
						$languages[] = $con;					
					}
				}
			}
			
			return $languages;
		}
	

	}

