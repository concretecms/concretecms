<?

defined('C5_EXECUTE') or die("Access Denied.");
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

