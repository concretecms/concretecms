<?
namespace Concrete\Core\Cache;
use \Zend\Cache\StorageFactory;

class Cache
{
    /** @var \Zend\Cache\|\Zend\Cache\Storage\StorageInterface */
    private $provider = null;
    private $enabled = true;

    /**
     * Returns a cache key
     * @param string $type Prefix of the cache entry
     * @param string $id ID of the cache entry
     * @return string The cache key
     */
    public static function key($type, $id)
    {
		return md5($type . $id);
	}

    /**
     * /**
     * Returns the cache provider. The cache will be initialized if it is not yet
     * @return \Zend\Cache\|\Zend\Cache\Storage\StorageInterface
     */
    public function getProvider()
    {
        if ($this->provider === null) {
            // cache provider has not yet been initialized
            $adapter = (defined('CACHE_LIBRARY')) ? CACHE_LIBRARY : 'filesystem';
            $this->provider = StorageFactory::factory(array(
                'adapter' => array(
                    'name' => $adapter,
                    'ttl' => CACHE_LIFETIME
                ),
                'options' => array(
                    'cache_dir' => DIR_FILES_CACHE,
                    'file_locking' => false
                ),
                'plugins' => array(
                    'exception_handler' => array('throw_exceptions' => false)
                )
            ));
        }

        return $this->provider;
    }

    public function disableCache()
    {
        $this->enabled = false;
    }

    public function enableCache()
    {
        $this->enabled = true;
    }
	
	public function disableLocalCache()
    {
		CacheLocal::get()->enabled = false;
	}
	public function enableLocalCache()
    {
		CacheLocal::get()->enabled = true;
	}

    public function set($type, $id, $obj, $expire = false)
    {
        // todo need to handle expire time

        $key = Cache::key($type, $id);
        $set = true;

        // cache it locally
        $loc = CacheLocal::get();
        if ($loc->enabled || $this->enabled) {
            if (is_object($obj)) {
                $r = clone $obj;
            } else {
                $r = $obj;
            }

            if ($loc->enabled) {
                $loc->cache[$key] = $r;
            }

            if ($this->enabled) {
                $set = $this->getProvider()->setItem($key, $r);
            }
        }

        return $set;
    }

    public function get($type, $id, $mustBeNewerThan = false) {
        $loc = CacheLocal::get();
        $key = Cache::key($type, $id);
        if ($loc->enabled && array_key_exists($key, $loc->cache)) {
            return $loc->cache[$key];
        }

        if ($this->enabled) {
            // todo expired cache items
            return $this->getProvider()->getItem($key);
        }

        return false;
    }

    /**
     * Checks if an item is in the cache
     * @param $type
     * @param $id
     * @param bool $mustBeNewerThan
     * @return bool
     */
    public function has($type, $id, $mustBeNewerThan = false)
    {
        $loc = CacheLocal::get();
        $key = Cache::key($type, $id);
        if ($loc->enabled && array_key_exists($key, $loc->cache)) {
            return true;
        }

        if ($this->enabled) {
            // todo expired cache items
            return $this->getProvider()->hasItem($key);
        }

        return false;
    }
	
	/** 
	 * Removes an item from the cache
	 */	
	public function delete($type, $id){
        $success = true;
        $key = Cache::key($type, $id);

		if ($this->enabled) {
			$success = $this->getProvider()->removeItem($key);
		}

		$loc = CacheLocal::get();
		if ($loc->enabled && isset($loc->cache[$key])) {
			unset($loc->cache[$key]);
		}

        return $success;
	}
	
	/** 
	 * Completely flushes the cache
	 */	
	public function flush()
    {
        $loc = CacheLocal::get();
        if ($loc->enabled) {
            $loc->flush();
        }

        if ($this->enabled) {
            // todo flush
        }
    }
		
}
