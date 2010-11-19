<?php 

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
class Database {

	public $db;
	
	/** 
	 * Checks to see whether query caching is enabled, and, if it is, uses ADODB's CacheExecute function. Otherwise a regular query is used.
	 * @param int $seconds
	 * @param string $query
	 */
	public function querycache($seconds, $q) {
		if (DB_USE_CACHE == true) {
			return $this->db->CacheExecute($seconds, $q);
		} else {
			return $this->db->Query($q);
		}
	}
	
	public function ensureEncoding() {
		if (APP_CHARSET == '') {
			return false;
		}
		
		$cd = $this->db->GetRow("show create database `{$this->db->database}`");
		if (!preg_match('/' . DB_CHARSET . '/i', $cd[1])) {
			$this->db->Execute("ALTER DATABASE `{$this->db->database}` character set " . DB_CHARSET);
		}
	}

	/** 
	 * Get's a Schema Object for a particular database object
	 */
	public function getADOSChema() {
		return new adoSchema($this->db);
	}

	/** 
	 * @access private 
	 */ 
	public function setDatabaseObject($db) {
		$this->db = $db;
	}
	
	public function getDatabaseObject() {
		return $this->db;
	}
	
	/** 
	 * Any item that isn't found in our wrapper class gets automatically sent our database object.
	 */
	public function __call($method, $args) {
		return call_user_func_array(array($this->db, $method), $args);
	}
	
	/** 
	 * Set's debug to true or false. True will display errors
	 * @param bool $_debug
	 */ 
	public function setDebug($_debug) {
		$this->db->debug = $_debug;
	}
	
	public function getDebug() {
		return $this->db->debug;
	}

	/** 
	 * Sets logging to true or false.
	 * @param bool $log
	 */ 
	public function setLogging($log) {
		$this->db->LogSQL($log);
		global $ADODB_PERF_MIN;
		$ADODB_PERF_MIN = 0;
	}


}