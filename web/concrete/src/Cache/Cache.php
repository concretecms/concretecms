<?
namespace Concrete\Core\Cache;
use PageCache;
use Events;
use Database as DB;
use \Zend\Cache\StorageFactory;
use Zend_Translate;
use Environment;
use CacheLocal as ConcreteCacheLocal;
use Loader;

class Cache {
	
	public static function key($type, $id) {
		return md5($type . $id);
	}
	
	public static function getLibrary() {
		static $cache;
		if (!isset($cache) && defined('DIR_FILES_CACHE')) {
			if (is_dir(DIR_FILES_CACHE) && is_writable(DIR_FILES_CACHE)) {
                $adapter = (defined('CACHE_LIBRARY')) ? CACHE_LIBRARY : 'filesystem';
                $cache = StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => $adapter,
                        'ttl' => CACHE_LIFETIME
                    )
                ));
                if ($adapter == 'filesystem') {
                    $cache->setOptions(array(
                        'cache_dir' => DIR_FILES_CACHE,
                        'file_locking' => false
                    ));
                }
			}
		}
		return $cache;
	}
	
	public function startup() {
		$cache = Cache::getLibrary();
	}
	
	public function disableCache() {
		$ca = Cache::getLibrary();
		if (is_object($ca)) {
			$ca->setCaching(false);
		}
	}
	
	public function enableCache() {
		$ca = Cache::getLibrary();
		if (is_object($ca)) {
            $ca->setCaching(true);
		}
	}
	
	public function disableLocalCache() {
		ConcreteCacheLocal::get()->enabled = false;
	}
	public function enableLocalCache() {
		ConcreteCacheLocal::get()->enabled = true;
	}
	
	/** 
	 * Inserts or updates an item to the cache
	 * the cache must always be enabled for (getting remote data, etc..)
	 */	
	public function set($type, $id, $obj, $expire = false) {
		$loc = ConcreteCacheLocal::get();
		if ($loc->enabled) {
			if (is_object($obj)) {
				$r = clone $obj;
			} else {
				$r = $obj;
			}
			$loc->cache[Cache::key($type, $id)] = $r;
		}
		$cache = Cache::getLibrary();
		if (!$cache) {
			return false;
		}
		$cache->save($obj, Cache::key($type, $id), array($type), $expire);
	}
	
	/** 
	 * Retrieves an item from the cache
	 */	
	public function get($type, $id, $mustBeNewerThan = false) {
		$loc = ConcreteCacheLocal::get();
		$key = Cache::key($type, $id);
		if ($loc->enabled && array_key_exists($key, $loc->cache)) {
			return $loc->cache[$key];
		}
			
		$cache = Cache::getLibrary();
		if (!$cache) {
			if ($loc->enabled) {
				$loc->cache[$key] = false;
			}
			return false;
		}
		
		// if mustBeNewerThan is set, we check the cache mtime
		// if mustBeNewerThan is newer than that time, we relinquish
		if ($mustBeNewerThan != false) {
			$metadata = $cache->getMetadatas($key);
			if ($metadata['mtime'] < $mustBeNewerThan) {
				// clear cache record and return false
				Cache::getLibrary()->remove($key);
				if ($loc->enabled) {
					$loc->cache[$key] = false;
				}
				return false;
			}
		}
		
		$loaded = $cache->load($key);
		if ($loc->enabled) {
			$loc->cache[$key] = $loaded;
		}
		return $loaded;
	}
	
	/** 
	 * Removes an item from the cache
	 */	
	public function delete($type, $id){
		$cache = \Cache::getLibrary();
		if ($cache) {
			$cache->remove(\Cache::key($type, $id));
		}

		$loc = ConcreteCacheLocal::get();
		if ($loc->enabled && isset($loc->cache[Cache::key($type, $id)])) {
			unset($loc->cache[Cache::key($type, $id)]);
		}
	}
	
	/** 
	 * Completely flushes the cache
	 */	
	public function flush() {
		$db = DB::get();

		// flush the CSS cache
		if (is_dir(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
			$fh = Loader::helper('file');
			$fh->removeAll(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
		}

		// flush the JS cache
		if (is_dir(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT)) {
			$fh = Loader::helper('file');
			$fh->removeAll(DIR_FILES_CACHE . '/' . DIRNAME_JAVASCRIPT);
		}
		
		$pageCache = PageCache::getLibrary();
		if (is_object($pageCache)) {
			$pageCache->flush();
		}
		
		if ($db->tableExists('Config')) {
			// clear the environment overrides cache
			$env = Environment::get();
			$env->clearOverrideCache();

			if(in_array('btCachedBlockRecord', $db->MetaColumnNames('Blocks'))) {
				$db->Execute('update Blocks set btCachedBlockRecord = null');
			}
			if ($db->tableExists('CollectionVersionBlocksOutputCache')) {
				$db->Execute('truncate table CollectionVersionBlocksOutputCache');
			}
		}
		
		$loc = ConcreteCacheLocal::get();
		$loc->cache = array();

		$cache = Cache::getLibrary();
		if ($cache) {
			$cache->flush();
		}

		$event = new \Symfony\Component\EventDispatcher\GenericEvent();
		$event->setArgument('cache', $cache);
		$ret = Events::dispatch('on_cache_flush', $event);

		return true;
	}
		
}
