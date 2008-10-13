<?

class Cache extends CacheTemplate {

	protected $cache = array();
	
	protected function key($type, $id) {
		return md5(strtolower($type . ':' . $id));
	}
	
	/** 
	 * Completely flushes the cache
	 */
	public function flush() {
		eaccelerator_clear();
	}
	
	/** 
	 * Removes an item from the cache
	 */
	public function delete($type, $id) {
		
		if (ENABLE_CACHE == false) {
			return false;
		}
		
		/*
		$l = new Log();
		$l->write('Deleting Cache for ' . $type . ' ' . $id);
		*/
		
		$k = $this->key($type, $id);
		$result = eaccelerator_rm($k);
		if ($result) {
			unset($this->cache[$k]);
		}
		return $result;
	}
	
	/** 
	 * Inserts or updates an item to the cache
	 */
	public function set($type, $id, $obj, $expire = 0) {
		if (ENABLE_CACHE == false) {
			return false;
		}
		
		/*
		$l = new Log();
		$l->write('Setting Cache for ' . $type . ' ' . $id);
		*/
		
		$k = $this->key($type, $id);
		$s = serialize($obj);
		$r = eaccelerator_put($k, $s, $expire);
		if ($r) {
			$this->cache[$k] = $obj;
		}
	}
	
	/** 
	 * Retrieves an item from the cache
	 */
	public function get($type, $id) {
		
		if (ENABLE_CACHE == false) {
			return false;
		}
		
		$k = $this->key($type, $id);
		if (isset($this->cache[$k])) {
			$value = $this->cache[$k];
		} else {
			$s = eaccelerator_get($k);
			$value = unserialize($s);
		}
		
		/*
		$l = new Log();
		$l->write('Getting Cache for ' . $type . ' ' . $id);
		*/
		
		if ($value === NULL) {
			$value = false;
		}
		
		$this->cache[$k] = $value;
		return $value;
	}
	
	/** 
	 * Retrieves information about the cache
	 */
	public function stats() {
		print_r(eaccelerator_info());
	}
}


