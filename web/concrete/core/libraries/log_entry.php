<?

defined('C5_EXECUTE') or die("Access Denied.");

 
class Concrete5_Library_LogEntry extends Object {
	
	public function getType() {return $this->logType;}
	public function getText() {return $this->logText;}
	public function getID() {return $this->logID;}
	public function getUserID() { return $this->logUserID;}
	
	public function getTimestamp($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			$timestamp = $dh->getLocalDateTime($this->timestamp);
		} else {
			$timestamp = $this->timestamp;
		}
		return $timestamp;
	}

	/** 
	 * Returns a log entry by ID
	 */
	public static function getByID($logID) {
		$db = Loader::db();
		$r = $db->Execute("select * from Logs where logID = ?", array($logID));
		if ($r) {
			$row = $r->FetchRow();
			$obj = new LogEntry();
			$obj->setPropertiesFromArray($row);
			return $obj;
		}
	}
	
	
}

