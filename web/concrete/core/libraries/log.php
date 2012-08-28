<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * A library for dealing with searchable logs.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Library_Log {

	private $log;
	private $logfile;
	private $name;
	private $session = false;
	private $sessionText = null;
	private $isInternal = false;
	
	public function __construct($log = null, $session = true, $internal = false) {
		$th = Loader::helper('text');
		if ($log == null) {
			$log = '';
		}
		$this->log = $log;
		$this->name = $th->unhandle($log);
		$this->session = $session;
		$this->isInternal = $internal;
	}
	
	public function write($message) {
		$this->sessionText .= $message . "\n";
		if (!$this->session) {
			$this->close();
		}
		return $this;
	}

	public static function addEntry($message, $namespace = false) {
		if (!$namespace) {
			$namespace = t('debug');
		}
		$l = new Log($namespace, false);
		$l->write($message);
	}
	
	/** 
	 * Removes all "custom" log entries - these are entries that an app owner has written and don't have a builtin C5 type
	 */
	public function clearCustom() {
		$db = Loader::db();
		$db->Execute("delete from Logs where logIsInternal = 0");
	}

	/** 
	 * Removes log entries by type- these are entries that an app owner has written and don't have a builtin C5 type
	 * @param string $type Is a lowercase string that uses underscores instead of spaces, e.g. sent_emails
	 */
	public function clearByType($type) {
		$db = Loader::db();
		$db->Execute("delete from Logs where logType = ?", array($type));
	}
	
	public function clearInternal() {
		$db = Loader::db();
		$db->Execute("delete from Logs where logIsInternal = 1");
	}

	
	/** 
	 * Removes all log entries
	 */
	public function clearAll() {
		$db = Loader::db();
		$db->Execute("delete from Logs");
	}

	
	public function close() {
		$u = new User();

		$v = array($this->log, htmlentities($this->sessionText, ENT_COMPAT, APP_CHARSET), $this->isInternal, $u->getUserID());
		$db = Loader::db();
		$db->Execute("insert into Logs (logType, logText, logIsInternal, logUserID) values (?, ?, ?, ?)", $v);
		$this->sessionText = '';
	}
	
	/** 
	 * Renames a log file and moves it to the log archive.
	 */
	public function archive() {

	}
	
	/** 
	 * Returns the total number of entries matching this type 
	 */
	public static function getTotal($keywords, $type) {
		$db = Loader::db();
		if ($keywords != '') {
			$kw = 'and logText like ' . $db->quote('%' . $keywords . '%');
		}
		if ($type != false) {
			$v = array($type);
			$r = $db->GetOne('select count(logID)  from Logs where logType = ? ' . $kw, $v);
		} else {
			$r = $db->GetOne('select count(logID)  from Logs where 1=1 ' . $kw);
		}
		return $r;
	}
	
	/** 
	 * Returns a list of log entries
	 */
	public static function getList($keywords, $type, $limit) {
		$db = Loader::db();
		if ($keywords != '') {
			$kw = 'and logText like ' . $db->quote('%' . $keywords . '%');
		}
		if ($type != false) {
			$v = array($type);
			$r = $db->Execute('select logID from Logs where logType = ? ' . $kw . ' order by timestamp desc, logID desc limit ' . $limit, $v);
		} else {
			$r = $db->Execute('select logID from Logs where 1=1 ' . $kw . ' order by timestamp desc, logID desc limit ' . $limit);
		}
		
		$entries = array();
		while ($row = $r->FetchRow()) {
			$entries[] = LogEntry::getByID($row['logID']);
		}
		return $entries;
	}
	
	/** 
	 * Returns an array of distinct log types
	 */
	public static function getTypeList() {
		$db = Loader::db();
		$lt = $db->GetCol("select distinct logType from Logs");
		if (!is_array($lt)) {
			$lt = array();
		}
		return $lt;
	}
	
	public function getName() { return $this->name;}
	
	/** 
	 * Returns all the log files in the directory
	 */
	public static function getLogs() {
		$db = Loader::db();
		$r = $db->GetCol('select distinct logType from Logs order by logType asc');
		return $r;
	}

}