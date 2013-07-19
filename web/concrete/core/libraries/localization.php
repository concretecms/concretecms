<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Library_Localization {

		private static $loc = null;

		public function init() {
			$loc = Localization::getInstance();
			$loc->getTranslate();
		}

		public static function getInstance() {
			if (null === self::$loc) {
				self::$loc = new self;
			}
			return self::$loc;
		}

		public static function changeLocale($locale) {
			$loc = Localization::getInstance();
			$loc->setLocale($locale);
		}

		public static function activeLocale() {
			$loc = Localization::getInstance();
			return $loc->getLocale();
		}

		protected $translate;

		public function __construct() {
			Loader::library('3rdparty/Zend/Date');
			Loader::library('3rdparty/Zend/Translate');
			$locale = defined('ACTIVE_LOCALE') ? ACTIVE_LOCALE : 'en_US';
			$this->setLocale($locale);
			Zend_Date::setOptions(array('format_type' => 'php'));
			if (ENABLE_TRANSLATE_LOCALE_EN_US || $locale != 'en_US') {
				$cache = Cache::getLibrary();
				if (is_object($cache)) {
					Zend_Translate::setCache($cache);
					Zend_Date::setOptions(array('cache'=>$cache));
				}
			}
		}

		public function setLocale($locale) {
			$localeNeededLoading = false;
			if (!ENABLE_TRANSLATE_LOCALE_EN_US && $locale == 'en_US' && isset($this->translate)) {
				unset($this->translate);
				return;
			}
			if (!(ENABLE_TRANSLATE_LOCALE_EN_US || $locale != 'en_US')) {
				return;
			}

			if (is_dir(DIR_LANGUAGES . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES . '/' . $locale;
			} elseif (is_dir(DIR_LANGUAGES_CORE . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES_CORE . '/' . $locale;
			}
			if (!$languageDir) {
				return;
			}

			$options = array(
				'adapter' => 'gettext',
				'content' => $languageDir,
				'locale'  => $locale
			);
			if (defined('TRANSLATE_OPTIONS')) {
				$_options = unserialize(TRANSLATE_OPTIONS);
				if (is_array($_options)) {
					$options = array_merge($options, $_options);
				}
			}

			if (!isset($this->translate)) {
				$this->translate = new Zend_Translate($options);
				$localeNeededLoading = true;
			} else {
				if (!in_array($locale, $this->translate->getList())) {
					$this->translate->addTranslation($options);
					$localeNeededLoading = true;
				}
				$this->translate->setLocale($locale);
			}
			if($localeNeededLoading) {
				Events::fire('on_locale_load', $locale);
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
			$loc = Localization::getInstance();
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

