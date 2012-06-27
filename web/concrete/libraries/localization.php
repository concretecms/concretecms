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
			Loader::library('3rdparty/Zend/Translate');
			$this->setLocale(defined('ACTIVE_LOCALE') ? ACTIVE_LOCALE : 'en_US');
			$cache = Cache::getLibrary();
			if (is_object($cache)) {
				Zend_Translate::setCache($cache);
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

/** Translate text (plural form).
* @param string $singular The singular form.
* @param string $plural The plural form.
* @param int $number The number.
* @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf
* @return string Returns the translated text.
* @example t2('%d child', '%d children', $n) will return translated '%d child' if $n is 1, translated '%d children' otherwise.
* @example t2('%d child', '%d children', $n, $n) will return translated '1 child' if $n is 1, translated '2 children' if $n is 2.
*/
function t2($singular, $plural, $number) {
	$zt = Localization::getTranslate();
	if(is_object($zt)) {
		$translated = $zt->plural($singular, $plural, $number);
	} else {
		$translated = ($number == 1) ? $singular : $plural;
	}
	if(func_num_args() == 3) {
		return $translated;
	}
	$arg = array();
	for($i = 3; $i < func_num_args(); $i++) {
		$arg[] = func_get_arg($i);
	}
	return vsprintf($translated, $arg);
}
