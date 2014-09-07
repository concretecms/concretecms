<?

namespace Concrete\Core\Cache;
use Core;

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
		return array();
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
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/local');
        if ($cache->isEnabled()) {
            $item = $cache->getItem($type . '/' . $id);
            if (!$item->isMiss()) {
                return $item->get();
            }
        }
	}

	public static function flush() {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/local');
        $cache->flush();
	}

	public static function delete($type, $id) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/local');
        if ($cache->isEnabled()) {
            $cache->delete($type . '/' . $id);
        }
	}

	public static function set($type, $id, $object) {
        /** @var \Concrete\Core\Cache\Cache $cache */
        $cache = Core::make('cache/local');

        if (!$cache->isEnabled()) {
            return false;
        }

        $item = $cache->getItem($type . '/' . $id);
        if (is_object($object)) {
            $item->set(clone $object);
        } else {
            $item->set($object);
        }
	}
}
