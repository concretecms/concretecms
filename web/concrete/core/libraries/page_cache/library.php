<?

defined('C5_EXECUTE') or die("Access Denied.");

abstract class Concrete5_Library_PageCache {

	static $library;

	public function deliver(PageCacheRecord $record) {
		if (defined('APP_CHARSET')) {
			header("Content-Type: text/html; charset=" . APP_CHARSET);
		}
		foreach ($record->getCacheRecordHeaders() as $header) {
			header($header);
		}

		print($record->getCacheRecordContent());
	}

	public static function getLibrary() {
		if (!PageCache::$library) {
			$class = Loader::helper('text')->camelcase(PAGE_CACHE_LIBRARY) . 'PageCache';
			PageCache::$library = new $class();
		}
		return PageCache::$library;
	}	

	/** 
	 * Note: can't use the User object directly because it might query the database
	 */
	public function shouldCheckCache(Request $req) {
		if ($_SESSION['uID'] > 0) {
			return false;
		}
		return true;
	}

	public function outputCacheHeaders(Page $c) {
		foreach ($this->getCacheHeaders($c) as $header) {
			header($header);
		}
	}

	public function getCacheHeaders(Page $c) {
		$lifetime = $c->getCollectionFullPageCachingLifetimeValue();
		$expires  = gmdate('D, d M Y H:i:', time() + $lifetime) . ' GMT';

		$headers  = array(
			'Pragma: public',
			'Cache-Control: s-maxage=' . $lifetime,
			'Cache-Control: max-age='  . $lifetime,
			'Expires: '                . $expires
		);

		return $headers;
	}

	public function shouldAddToCache(View $v) {
		$c = $v->getCollectionObject();
		if (!is_object($c)) {
			return false;
		}

		$cp = new Permissions($c);
		if (!$cp->canViewPage()) {
			return false;
		}

		$u = new User();

		$allowedControllerActions = array('view');
		if (is_object($v->controller)) {
			if (!in_array($v->controller->getTask(), $allowedControllerActions)) {
				return false;
			}
		}
		
		if (!$c->getCollectionFullPageCaching()) {
			return false;
		}

		if ($u->isRegistered() || $_SERVER['REQUEST_METHOD'] == 'POST') {
			return false;
		}

		if ($c->isGeneratedCollection()) {
			if ((is_object($v->controller) && (!$v->controller->supportsPageCache())) || (!is_object($v->controller))) {
				return false;
			}
		}	

		if ($c->getCollectionFullPageCaching() == 1 || FULL_PAGE_CACHE_GLOBAL === 'all') {
			// this cache page at the page level
			// this overrides any global settings
			return true;
		}

		if (FULL_PAGE_CACHE_GLOBAL !== 'blocks') {
			// we are NOT specifically caching this page, and we don't 
			return false;
		}

		$blocks = $c->getBlocks();
		array_merge($c->getGlobalBlocks(), $blocks);

		foreach($blocks as $b) {
			$controller = $b->getInstance();
			if (!$controller->cacheBlockOutput()) {
				return false;
			}
		}
		return true;
	}

	public function getCacheKey($mixed) {
		if ($mixed instanceof Page) {
			if ($mixed->getCollectionPath() != '') {
				return urlencode(trim($mixed->getCollectionPath(), '/'));
			} else if ($mixed->getCollectionID() == HOME_CID) {
				return '!' . HOME_CID;
			}			
		} else if ($mixed instanceof Request) {
			if ($mixed->getRequestPath() != '') {
				return urlencode(trim($mixed->getRequestPath(), '/'));
			} else if ($mixed->getRequestCollectionID() == HOME_CID) {
				return '!' . HOME_CID;
			}			
		} else if ($mixed instanceof PageCacheRecord) {
			return $mixed->getCacheRecordKey();
		}
	}

	/*
	public function getPageContent(Page $c) {
		ob_start();
		$v = View::getInstance();
		$v->disableEditing();
		$v->setCollectionObject($c);
		$req = Request::get();
		$req->setCustomRequestUser(false);				
		$req->setCurrentPage($c);
		$v->render($c);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	*/

	abstract public function getRecord($mixed);
	abstract public function set(Page $c, $content);
	abstract public function purgeByRecord(PageCacheRecord $rec);
	abstract public function purge(Page $c);
	abstract public function flush();

}