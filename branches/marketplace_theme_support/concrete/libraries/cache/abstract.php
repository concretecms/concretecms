<?
	
	// the local cache stores objects that are reused within the scope of the page
	// so we don't even have to hit eaccelerator_get or whatever more than once
	class CacheLocal {

		public $cache = array();
		
		public static function get() {
			static $instance;
			if (!isset($instance)) {
				$v = __CLASS__;
				$instance = new $v;
			}
			return $instance;
		}
	}
		
	abstract class CacheTemplate {
		
		
		protected function key($type, $id) {
			return md5(strtolower($type . ':' . $id));
		}
		
		abstract public function delete($type, $id);
		abstract public function flush();
		abstract public function set($type, $id, $obj, $expire = 0);
		abstract public function get($type, $id);
		//abstract public function stats();
	
	}