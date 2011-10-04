<?
	
	class Localization {
	
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

		protected $translate;

		public function __construct() {
			if (defined('ACTIVE_LOCALE') && ACTIVE_LOCALE != 'en_US') {
				Loader::library('3rdparty/Zend/Translate');
				$cache = Cache::getLibrary();
				if (is_object($cache)) {
					Zend_Translate::setCache($cache);
				}
				if (ACTIVE_LOCALE != 'en_US') {
					if (is_dir(DIR_BASE . '/languages/' . ACTIVE_LOCALE)) {
						$this->translate = new Zend_Translate('gettext', DIR_BASE . '/languages/' . ACTIVE_LOCALE, ACTIVE_LOCALE);
					}
				}
			}
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
				$languages = array_merge($languages, $fh->getDirectoryContents(DIR_LANGUAGES));
			}
			if (file_exists(DIR_LANGUAGES_CORE)) {
				$languages = array_merge($languages, $fh->getDirectoryContents(DIR_LANGUAGES_CORE));
			}
			
			$langtmp = array();
			foreach($languages as $lang) {
				if ($lang != DIRNAME_LANGUAGES_SITE_INTERFACE) {
					$langtmp[] = $lang;
				}
			}
			
			return $langtmp;
		}
	

	}

	function t($text) {
		$zt = Localization::getTranslate();
		if (func_num_args() == 1) {
			if (is_object($zt)) {
				return $zt->_($text);
			} else {
				return $text;
			}
		}
		
		$arg = array();
	    for($i = 1 ; $i < func_num_args(); $i++) {
	        $arg[] = func_get_arg($i); 
	    }
		if (is_object($zt)) {
			return vsprintf($zt->_($text), $arg);
		} else {
			return vsprintf($text, $arg);
		}
	}

