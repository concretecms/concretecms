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
 	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ValidationStringsHelper {	
	
		
		/**
		 * Validates an email address
		 * @param string $address
		 * @return bool $isvalid
		 */
		public function email($em) {
			return eregi("^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $em);
		}
		
		/**
		 * Returns true on whether the passed field is completely alpha-numeric
		 * @param string $field
		 * @return bool
		 */
		public function alphanum($field) {
			return !eregi('[^A-Za-z0-9]', $field);
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