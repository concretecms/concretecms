<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helpful functions for validating numbers. 
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
class ValidationNumbersHelper {

	/** 
	 * Tests whether the passed item is an integer. Since this is frequently used by the form helper we're not checking
	 * whether the TYPE of data is an integer, but whether the passed argument represents a valid text/string version of an
	 * integer (so we'll use a regular expression)
	 * @param $int
	 * @return bool
	 */
	public function integer($data) {
		$id = (string) intval($data);
		if ($id == $data && $id > 0) {
			return true;
		}
		return false;
	}
	
}