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

