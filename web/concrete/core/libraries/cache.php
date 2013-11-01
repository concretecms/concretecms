<?

class Concrete5_Library_Cache {
	
	public static function key($type, $id) {
		return md5($type . $id);
	}
	
	public function getLibrary() {
		static $cache;
		if (!isset($cache) && defined('DIR_FILES_CACHE')) {
			if (is_dir(DIR_FILES_CACHE) && is_writable(DIR_FILES_CACHE)) {
				Loader::library('3rdparty/Zend/Cache');

				$frontendOptions = array(
					'lifetime' => CACHE_LIFETIME,
					'automatic_serialization' => true,
					'cache_id_prefix' => CACHE_ID		
				);
				$backendOptions = array(
					'read_control' => false,
					'cache_dir' => DIR_FILES_CACHE,
					'file_locking' => false
				);
				if (defined('CACHE_BACKEND_OPTIONS')) {
					$opts = unserialize(CACHE_BACKEND_OPTIONS);
					foreach($opts as $k => $v) {
						$backendOptions[$k] = $v;
					}
				}
				if (defined('CACHE_FRONTEND_OPTIONS')) {
					$opts = unserialize(CACHE_FRONTEND_OPTIONS);
					foreach($opts as $k => $v) {
						$frontendOptions[$k] = $v;
					}
				}
				if (!defined('CACHE_LIBRARY') || (defined("CACHE_LIBRARY") && CACHE_LIBRARY == "default")) {
					define('CACHE_LIBRARY', 'File');
				}
				$customBackendNaming = false;
				if (CACHE_LIBRARY == 'Zend_Cache_Backend_ZendServer_Shmem') {
					$customBackendNaming = true;
				}
				$cache = Zend_Cache::factory('Core', CACHE_LIBRARY, $frontendOptions, $backendOptions, false, $customBackendNaming);
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
			$ca->setOption('caching', false);
		}
	}
	
	public function enableCache() {
		$ca = Cache::getLibrary();
		if (is_object($ca)) {
			$ca->setOption('caching', true);
		}
	}
	
	public function disableLocalCache() {
		CacheLocal::get()->enabled = false;
	}
	public function enableLocalCache() {
		CacheLocal::get()->enabled = true;
	}
	
	/** 
	 * Inserts or updates an item to the cache
	 * the cache must always be enabled for (getting remote data, etc..)
	 */	
	public function set($type, $id, $obj, $expire = false) {
		$loc = CacheLocal::get();
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
		$loc = CacheLocal::get();
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
	 * not used. Good idea but doesn't work with all cache layers and on large caches is VERY slow.

	public function deleteType($type) {
		Cache::getLibrary()->clean('matchingTag', array($type));
		$loc = CacheLocal::get();
		$loc->enabled = false;
	}
	
	*/
	
	/** 
	 * Removes an item from the cache
	 */	
	public function delete($type, $id){
		$cache = Cache::getLibrary();
		if ($cache) {
			$cache->remove(Cache::key($type, $id));
		}

		$loc = CacheLocal::get();
		if ($loc->enabled && isset($loc->cache[Cache::key($type, $id)])) {
			unset($loc->cache[Cache::key($type, $id)]);
		}
	}
	
	/** 
	 * Completely flushes the cache
	 */	
	public function flush() {
		$db = Loader::db();
		$r = $db->MetaTables();

		// flush the CSS cache
		if (is_dir(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
			$fh = Loader::helper("file");
			$fh->removeAll(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
		}
		
		$pageCache = PageCache::getLibrary();
		if (is_object($pageCache)) {
			$pageCache->flush();
		}
		
		if (in_array('Config', $r)) {
			// clear the environment overrides cache
			$env = Environment::get();
			$env->clearOverrideCache();

			if(in_array('btCachedBlockRecord', $db->MetaColumnNames('Blocks'))) {
				$db->Execute('update Blocks set btCachedBlockRecord = null');
			}
			if (in_array('CollectionVersionBlocksOutputCache', $r)) {
				$db->Execute('truncate table CollectionVersionBlocksOutputCache');
			}
		}
		
		$loc = CacheLocal::get();
		$loc->cache = array();

		$cache = Cache::getLibrary();
		if ($cache) {
			$cache->setOption('caching', true);
			$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
		}
		Events::fire('on_cache_flush', $cache);
		return true;
	}
		
}
