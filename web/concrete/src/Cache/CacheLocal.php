<?

namespace Concrete\Core\Cache;
class CacheLocal {

	public $cache = array();
	public $enabled = true; // disabled because of weird annoying race conditions. This will slow things down but only if you don't have zend cache active.


    /**
     * Creates a cache key based on the group and id by running it through md5
     * @param string $group Name of the cache group
     * @param string $id Name of the cache item ID
     * @return string The cache key
     */
    public static function key($group, $id)
    {
        return md5($group . $id);
    }

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
		$key = self::key($type, $id);
		if ($loc->enabled && array_key_exists($key, $loc->cache)) {
			return $loc->cache[$key];
		}
	}

	public static function flush() {
		$loc = CacheLocal::get();
		$loc->cache = array();
	}

	public static function delete($type, $id) {
		$loc = CacheLocal::get();
		$key = self::key($type, $id);
		if ($loc->enabled && array_key_exists($key, $loc->cache)) {
			unset($loc->cache[$key]);
		}
	}

	public static function set($type, $id, $object) {
		$loc = CacheLocal::get();
		if (!$loc->enabled) {
			return false;
		}

		$key = self::key($type, $id);
		if (is_object($object)) {
			$r = clone $object;
		} else {
			$r = $object;
		}

		$loc->cache[$key] = $r;
	}
}
