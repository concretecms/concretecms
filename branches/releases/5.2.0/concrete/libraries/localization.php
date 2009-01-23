<?php 
	
	class Localization {
	
		public function isAvailable() {
			return function_exists('textdomain');
		}
		
		public function setDomain($path) {
			if (Localization::isAvailable()) {
				if (is_dir($path . '/' . DIRNAME_LANGUAGES)) {
					bindtextdomain(LANGUAGE_DOMAIN_CORE, $path . '/' . DIRNAME_LANGUAGES);
					textdomain(LANGUAGE_DOMAIN_CORE);	
					bind_textdomain_codeset(LANGUAGE_DOMAIN_CORE, "UTF-8");				
				}
			}
		}
		
		/** 
		 * Resets the text domain to the default localization. This should be called after we branch out to any blocks, etc...
		 */
		public function reset() {
			if (Localization::isAvailable()) {
				Localization::setDomain(DIR_BASE);
			}
		}
		
		public function init() {
			if (Localization::isAvailable()) {
				$l = explode(',', str_replace(' ', '', LOCALE));
				setlocale(LC_ALL, $l);
				if (!ini_get('safe_mode')) {
					putenv('LC_ALL=' . LOCALE);
				}
				Localization::reset();		
			}
		}
	}
	
	Localization::init();
	if (!Localization::isAvailable()) {
		function gettext($string) {return $string;}
		function _($string) {return $string;}
	}

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
