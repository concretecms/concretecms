<? defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Functions useful for validating strings
 * @package Helpers
 * @category Concrete
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

class Concrete5_Helper_Validation_Strings {	

	
	/**
	 * Validates an email address
	 * @param string $address
	 * @return bool $isvalid
	 */
	public function email($em, $testMXRecord = false) {
		if (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $em, $matches)) {
			if ($testMXRecord) {
				list($username, $domain) = split("@", $em);
				return getmxrr($domain, $mxrecords);
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Returns true on whether the passed field is completely alpha-numeric
	 * @param string $field
	 * @param bool $allow_spaces whether or not spaces are permitted in the field contents
	 * @param bool $allow_dashes whether or not dashes (-) are permitted in the field contents
	 * @return bool
	 */
	public function alphanum($field, $allow_spaces = false, $allow_dashes = false) {
		if ($allow_spaces && $allow_dashes) {
			return !preg_match("/[^A-Za-z0-9 \-]/", $field);
		} else if ($allow_spaces) {
			return !preg_match("/[^A-Za-z0-9 ]/", $field);
		} else if ($allow_dashes) {
			return !preg_match("/[^A-Za-z0-9\-]/", $field);
		} else {
			return !preg_match('/[^A-Za-z0-9]/', $field);
		}
	}
	
	/** 
	 * Returns true if the passed field is a valid "handle" (e.g. only letters, numbers, or a _ symbol
	 */
	public function handle($handle) {
		return !preg_match("/[^A-Za-z0-9\_]/", $handle);
	}

	
	/**
	 * Returns false if the string is empty (including trim())
	 * @param string $field
	 * @return bool
	 */
	public function notempty($field) {
		return ((is_array($field) && count($field) > 0) || (is_string($field) && trim($field) != ''));
	}	
	
	/** 
	 * Returns true on whether the passed string is larger or equal to the passed length
	 * @param string $str
	 * @param int $length
	 * @return bool
	 */
	public function min($str, $length) {
		return strlen(trim($str)) >= $length;
	}
	
	/** 
	 * Returns true on whether the passed is smaller or equal to the passed length
	 * @param string $str
	 * @param int $length
	 * @return bool
	 */
	public function max($str, $length) {
		return strlen(trim($str)) <= $length;
	}
	
	/**
	 * Returns 0 if there are no numbers in the string, or returns the number of numbers in the string
	 * @param string $str
	 * @return int
	 */
	public function containsNumber($str) {
		return strlen(trim(preg_replace('/([^0-9]*)/', '', $str)));
	}
	
	/**
	 * Returns 0 if there are no upper case letters in the string, or returns the number of upper case letters in the string
	 * @param string $str
	 * @return int
	 */
	public function containsUpperCase($str) {
		return strlen(trim(preg_replace('/([^A-Z]*)/', '', $str)));
	}

	/**
	 * Returns 0 if there are no lower case letters in the string, or returns the number of lower case letters in the string
	 * @param string $str
	 * @return int
	 */
	public function containsLowerCase($str) {
		return strlen(trim(preg_replace('/([^a-z]*)/', '', $str)));
	}

	/**
	 * Returns 0 if there are no symbols in the string, or returns the number of symbols in the string
	 * @param string $str
	 * @return int
	 */	
	public function containsSymbol($str) {
		return strlen(trim(preg_replace('/([a-zA-Z0-9]*)/', '', $str))); //we replace a-z and numbers and see if there is anything left.
	}
	
	/**
	 * Returns true if the string contains another string
	 * @param string $str
	 * @param array $cont
	 * @return bool
	 */
	public function containsString($str, $cont = array()) {
		$arr = (!is_array($cont)) ? array($cont) : $cont; // turn the string into an array
		foreach ($arr as $item) {
			if (strstr($str, $item) !== false) {
				return true;
			}
		}
		return false;
	}
}