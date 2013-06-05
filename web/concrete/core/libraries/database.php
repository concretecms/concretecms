<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A database wrapper that should hopefully allow us to switch abstractions layers better in the future, or at least pave the way for such functionality
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Library_Database {

	/** 
	 * Get's a Schema Object for a particular database object
	 */
	public static function getADOSChema() {
		$db = Loader::db();
		return new adoSchema($db);
	}

	/** 
	 * Set's debug to true or false. True will display errors
	 * @param bool $_debug
	 */ 
	public function setDebug($_debug) {
		$db = Loader::db();
		$db->debug = $_debug;
	}
	
	public function getDebug() {
		$db = Loader::db();
		return $db->debug;
	}

	/** 
	 * Sets logging to true or false.
	 * @param bool $log
	 */ 
	public function setLogging($log) {
		$db = Loader::db();
		$db->LogSQL($log);
		global $ADODB_PERF_MIN;
		$ADODB_PERF_MIN = 0;
	}

	public static function ensureEncoding() {
		if (!defined('DB_CHARSET') || DB_CHARSET == '') { 
			return false;
		}

		$db = Loader::db();
		$q = "ALTER DATABASE `{$db->database}` character set " . DB_CHARSET;
		if (!defined('DB_COLLATE') || DB_COLLATE != '') { 
			$q .= " COLLATE " . DB_COLLATE;
		}
		$db->Execute($q);
	}


}