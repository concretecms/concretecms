<?php 

/**
 * @package Helpers
 * @subpackage Validation
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for validating strings
 * @package Helpers
 * @category Concrete
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationStringsHelper {	

	
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
	 * @return bool
	 */
	public function alphanum($field, $allow_spaces = false) {
		if($allow_spaces) {
			return !preg_match("/[^A-Za-z0-9 ]/", $field);
		} else {
			return !preg_match('/[^A-Za-z0-9]/', $field);
		}
	}
	
	/**
	 * Returns false if the string is empty (including trim())
	 * @param string $field
	 * @return bool
	 */
	public function notempty($field) {
		return (trim($field) != '');
	}	
	
	/** 
	 * Returns true on whether the passed string is larger or equal to the passed length
	 * @param string $str
	 * @param int $length
	 * @return bool
	 */
	public function min($str, $num) {
		return strlen(trim($str)) >= $num;
	}
	
	/** 
	 * Returns true on whether the passed is smaller or equal to the passed length
	 * @param string $str
	 * @param int $length
	 * @return bool
	 */
	public function max($str, $num) {
		return strlen(trim($str)) <= $num;
	}

}

?>