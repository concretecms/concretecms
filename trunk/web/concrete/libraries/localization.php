<?

	function t($text) {
		if (func_num_args() == 1) {
			return gettext($text);
		}
		
		$arg = array();
	    for($i = 1 ; $i < func_num_args(); $i++) {
	        $arg[] = func_get_arg($i); 
	    }
	    return vsprintf(gettext($text), $arg);
	}
	
	class Localization {
	
	
		public function setDomain($path) {
			if (is_dir($path . '/' . DIRNAME_LANGUAGES)) {
				bindtextdomain(LANGUAGE_DOMAIN_CORE, $path . '/' . DIRNAME_LANGUAGES);
				textdomain(LANGUAGE_DOMAIN_CORE);
			}
		}
		
		/** 
		 * Resets the text domain to the default localization. This should be called after we branch out to any blocks, etc...
		 */
		public function reset() {
			Localization::setDomain(DIR_BASE);
		}
		
		public function init() {
			setlocale(LC_ALL, LOCALE);
			putenv('LC_ALL=' . LOCALE);
			Localization::reset();		
		}
	}
	
	Localization::init();