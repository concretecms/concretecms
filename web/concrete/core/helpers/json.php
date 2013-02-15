<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for working with JSON (JavaScript Object Notation)
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Json {

	
	/** 
	 * Decodes a JSON string
	 * @param string $string The string to be decoded.
	 * @param bool $assoc When true, returned objects will be converted into associative arrays (default: false). 
	 * @return string
	 */
	public function decode($string, $assoc = false) {
		if (function_exists('json_decode')) {
			return json_decode($string, $assoc);
		} else {
			Loader::library('3rdparty/JSON/JSON');
			$sjs = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
			return $sjs->decode($string);
		}
	}
	
	
	/** 
	 * Encodes a data structure into a JSON string
	 * @param string $mixed
	 * @return string
	 */
	public function encode($mixed) {
		if (function_exists('json_encode')) {
			return json_encode($mixed);
		} else {
			Loader::library('3rdparty/JSON/JSON');
			$sjs = new Services_JSON();
			return $sjs->encode($mixed);
		}
	}
	


}