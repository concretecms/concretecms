<?

class Concrete5_Library_CacheLocal {

	public $cache = array();
	public $enabled = true; // disabled because of weird annoying race conditions. This will slow things down but only if you don't have zend cache active.
	
	public function getEntries() {
		return $this->cache;
	}

	public static function get() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}
	
	public static function getEntry($type, $id) {
		$loc = CacheLocal::get();
		$key = Cache::key($type, $id);
		if ($loc->enabled && array_key_exists($key, $loc->cache)) {
			return $loc->cache[$key];
		}
	}
		
	public static function delete($type, $id) {
		$loc = CacheLocal::get();
		$key = Cache::key($type, $id);
		if ($loc->enabled && array_key_exists($key, $loc->cache)) {
			unset($loc->cache[$key]);
		}
	}	

	public static function flush() {
		$loc = CacheLocal::get();
		if ($loc->enabled) {
			$loc->cache = array();
		}
	}
	
	public static function set($type, $id, $object) {
		$loc = CacheLocal::get();
		if (!$loc->enabled) {
			return false;
		}

		$key = Cache::key($type, $id);
		if (is_object($object)) {
			$r = clone $object;
		} else {
			$r = $object;
		}
		
		$loc->cache[$key] = $r;
	}
}
