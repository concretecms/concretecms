<?

function concrete_log_query($q) { 
	$l = Log::getInstance();
	$l->addQuery($q);
}

class Log {

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
