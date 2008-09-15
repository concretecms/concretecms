<?

	function t($text) {
		$arg = array();
	    for($i = 1 ; $i < func_num_args(); $i++) {
	        $arg[] = func_get_arg($i); 
	    }
	    return vsprintf(gettext($text), $arg);
	}