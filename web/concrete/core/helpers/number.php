<?php defined('C5_EXECUTE') or die('Access Denied');

class Concrete5_Helper_Number {

	/** Rounds the value only out to its most significant digit.
	* @param string $value
	* @return number
	*/
	public function flexround($value) {
		$v = explode('.', $value);
		$p = 0;
		for ($i = 0; $i < strlen($v[1]); $i++) {
			if (substr($v[1], $i, 1) > 0) {
				$p = $i+1;
			}
		}
		return round($value, $p);
	}

	/** Returns the Zend_Locale instance for the current locale.
	* @return Zend_Locale
	*/
	protected function getZendLocale() {
		static $zl;
		$locale = Localization::activeLocale();
		if((!isset($zl)) || ($locale != $zl->toString())) {
			$zl = new Zend_Locale($locale);
		}
		return $zl;
	}

	/** Checks if a given string is valid representation of a number in the current locale.
	* @return bool
	*/
	public function isNumber($string) {
		return Zend_Locale_Format::isNumber($string, array('locale' => $this->getZendLocale()));
	}

	/** Checks if a given string is valid representation of an integer in the current locale.
	* @return bool
	*/
	public function isInteger($string) {
		return Zend_Locale_Format::isInteger($string, array('locale' => $this->getZendLocale()));
	}

	/** Format a number with grouped thousands and localized decimal point/thousands separator.
	* @param number $number The number being formatted.
	* @param int|null $precision [default: null] The wanted precision; if null or not specified the complete localized number will be returned.
	* @return string
	*/
	public function format($number, $precision = null) {
		if(!is_numeric($number)) {
			return $number;
		}
		$options = array('locale' => $this->getZendLocale());
		if(is_numeric($precision)) {
			$options['precision'] = $precision;
		}
		return Zend_Locale_Format::toNumber($number, $options);
	}


	/** Parses a localized number representation and returns the number (or null if $string is not a valid number representation).
	* @param string $string The number representation to parse.
	* @param bool $trim [default: true] Remove spaces and new lines at the start/end of $string?
	* @param int|null $precision [default: null] The wanted precision; if null or not specified the complete number will be returned.
	* @return null|number
	*/
	public function unformat($string, $trim = true, $precision = null) {
		if(is_int($string) || is_float($string)) {
			return is_numeric($precision) ? round($string, $precision) : $string;
		}
		if(!is_string($string)) {
			return null;
		}
		if($trim) {
			$string = trim($string);
		}
		if(!(strlen($string) && $this->isNumber($string))) {
			return null;
		}
		$options = array('locale' => $this->getZendLocale());
		if(is_numeric($precision)) {
			$options['precision'] = $precision;
		}
		return Zend_Locale_Format::getNumber($string, $options);
	}

}
