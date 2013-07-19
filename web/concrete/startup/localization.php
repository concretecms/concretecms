<?
defined('C5_EXECUTE') or die("Access Denied.");

/** Translate text (simple form).
* @param string $text The text to be translated.
* @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf.
* @return string Returns the translated text.
* @example t('Hello %s') will return translation for 'Hello %s' (example for Italian 'Ciao %s').
* @example t('Hello %s', 'John') will return translation for 'Hello %s' (example: 'Ciao %s'), using 'John' for printf (so the final result will be 'Ciao John' for Italian).
*/
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
		$translated = $number == 1 ? $singular : $plural;
	}
	$arg = array_slice(func_get_args(), 3);
	if ($arg) {
		return vsprintf($translated, $arg);
	}
	return vsprintf($translated, $number);
}

/** Translate text (simple form) with a context.
* @param string $context A context, useful for translators to better understand the meaning of the text to be translated.
* @param string $text The text to be translated.
* @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf.
* @return string Returns the translated text.
* @example tc('Recipient', 'To %s') will return translation for 'To %s' (example for Italian 'A %s').
* @example tc('End date', 'To %s') will return translation for 'To %s' (example for Italian 'Fino al %s').
* @example tc('Recipient', 'To %s', 'John') will return translation for 'To %s' (example: 'A %s'), using 'John' for printf (so the final result will be 'A John' for Italian).
* @example tc('End date', 'To %s', '01/01/2000') will return translation for 'To %s' (example: 'Fino al %s'), using '01/01/2000' for printf (so the final result will be 'Fino al 01/01/2000' for Italian).
*/
function tc($context, $text) {
	$zt = Localization::getTranslate();
	if (is_object($zt)) {
		$msgid = $context . "\x04" . $text;
		$msgtxt = $zt->_($msgid);
		if($msgtxt != $msgid) {
			$text = $msgtxt;
		}
	}
	if (func_num_args() == 2) {
		return $text;
	}
	$arg = array();
	for($i = 2 ; $i < func_num_args(); $i++) {
		$arg[] = func_get_arg($i);
	}
	return vsprintf($text, $arg);
}
