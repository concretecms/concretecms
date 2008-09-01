<?

/**
 * @access private
 */
function concrete_log_query($q) { 
	$l = DBLog::getInstance();
	$l->addQuery($q);
}

class DBLog {

	public function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}	
	
	protected $queries = array();
	
	public function addQuery($q) {
		$this->queries[] = $q;
	}
	
	public function getQueries() {
		return $this->queries;
	}

}

class Log {

	private $log;
	private $logfile;
	private $name;
	private $sessionStarted = false;
	private $logDelimiter = "\n-------------------------------\n";
	
	public function __construct($log, $createIfNotFound = true) {
		if (!file_exists(DIR_FILES_LOGS)) {
			if (!mkdir(DIR_FILES_LOGS)) {
				throw new Exception('Unable to create logs directory');
			}
		}
		if (file_exists(DIR_FILES_LOGS . '/' . $log) || $createIfNotFound) {
			$th = Loader::helper('text');
			$this->log = $log;
			$this->name = $th->uncamelcase(substr($log, 0, strpos($log, '.log')));
			$this->logfile = DIR_FILES_LOGS . '/' . $log;
		}
	}
	
	public function write($message) {
		$fh = Loader::helper('file');
		if (!$this->sessionStarted) {
			$fh->append($this->logfile, $this->logDelimiter . "\n");
			$this->sessionStarted = true;
		}
		$message = date('Y-m-d H:i:s') . ' ' . $message . "\n";
		$fh->append($this->logfile, $message);
	}
	
	public function close() {
		$this->sessionStarted = false;
	}
	
	public function getContents() {
		$fh = Loader::helper('file');
		$contents = $fh->getContents($this->logfile);
		return $contents;
	}
	
	/** 
	 * Renames a log file and moves it to the log archive.
	 */
	public function archive() {
		if (!file_exists(DIR_FILES_LOGS . '/' . DIRNAME_LOGS_ARCHIVE)) {
			if (!mkdir(DIR_FILES_LOGS . '/' . DIRNAME_LOGS_ARCHIVE)) {
				throw new Exception('Unable to create logs archive directory');
			}
		}
		$archiveFilename = $this->log . '.' . time();
		copy($this->logfile, DIR_FILES_LOGS . '/' . DIRNAME_LOGS_ARCHIVE . '/' . $archiveFilename);
		$fh = Loader::helper('file');
		$fh->clear($this->logfile);
		$this->write('File archived to ' . $archiveFilename);
	}
	
	public function getName() { return $this->name;}
	public function getFileName() {return $this->log;}
	
	/** 
	 * Returns all the log files in the directory
	 */
	public static function getLogs() {
		$fh = Loader::helper('file');
		$logs = $fh->getDirectoryContents(DIR_FILES_LOGS, array(DIRNAME_LOGS_ARCHIVE));
		
		$loglist = array();
		foreach($logs as $logfile) {
			$l = new Log($logfile);
			$loglist[] = $l;
		}
		return $loglist;
	}

}