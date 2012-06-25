<?

class Concrete5_Library_CacheLocal {

	public $cache = array();
	public $enabled = true; // disabled because of weird annoying race conditions. This will slow things down but only if you don't have zend cache active.
	
	public static function get() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}
}
