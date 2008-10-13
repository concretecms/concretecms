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
	
	setlocale(LC_ALL, LANGUAGE);
	putenv('LC_ALL=' . LANGUAGE);
	bindtextdomain("messages", DIR_BASE_CORE . '/locale');
	textdomain('messages');