<?
namespace Concrete\Core\Application;

use \Illuminate\Container\Container;
use \Concrete\Core\Http\Request;
use \Concrete\Core\Cache\Page\PageCache;

class Application extends Container {

	protected $installed = false;

	/**
	 * Returns true if concrete5 is installed, false if it has not yet been
	 */
	public function isInstalled() {
		return $this->installed;
	}

	/**
	 * Checks to see whether we should deliver a concrete5 response from the page cache
	 */
	public function checkPageCache(Request $request) {
		if ($this->isInstalled()) {
			$library = PageCache::getLibrary();
			if ($library->shouldCheckCache($request)) {
			    $record = $library->getRecord($request);
			    if ($record instanceof PageCacheRecord) {
			    	if ($record->validate()) {
				    	return $library->deliver($record);
				    }
			    }
			}	
		}
		return false;
	}

	public function __construct() {
		if (defined('CONFIG_FILE_EXISTS')) {
			$this->installed = true;
		}
	}


	public function shutdown() {
		$db = Loader::db();
		$db->close();
		if (defined('ENABLE_OVERRIDE_CACHE') && ENABLE_OVERRIDE_CACHE) {
			Environment::saveCachedEnvironmentObject();
		} else if (defined('ENABLE_OVERRIDE_CACHE') && (!ENABLE_OVERRIDE_CACHE)) {
			$env = Environment::get();
			$env->clearOverrideCache();
		}
	}


}