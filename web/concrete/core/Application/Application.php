<?
namespace Concrete\Core\Application;

use \Illuminate\Container\Container;
use \Concrete\Core\Cache\Page\PageCache;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Router;
use Request;
use Environment;
use Database;

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

	/**
	 * Initializes concrete5
	 */
	public function __construct() {
		if (defined('CONFIG_FILE_EXISTS')) {
			$this->installed = true;
		}
	}

	/**
	 * Using the configuration value, determines whether we need to redirect to a URL with
	 * a trailing slash or not.
	 * @return void 
	 */
	public function handleURLSlashes() {
		$r = Request::getInstance();
		$pathInfo = $r->getPathInfo();
		if (strlen($pathInfo) > 1) {
			$path = trim($pathInfo, '/');
			$redirect = '/' . $path;
			if (URL_USE_TRAILING_SLASH) {
				$redirect .= '/';
			}
			if ($pathInfo != $redirect) {
				Redirect::url(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/' . $path . ($r->getQueryString() ? '?' . $r->getQueryString() : ''))->send();
			}
		}
	}

	/**
	 * If we have REDIRECT_TO_BASE_URL enabled, we need to honor it here.
	 */
	public function handleBaseURLRedirection() {
		if (REDIRECT_TO_BASE_URL) {
			$protocol = 'http://';
			$base_url = BASE_URL;
			if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
				$protocol = 'https://';
				if (defined('BASE_URL_SSL')) {
					$base_url = BASE_URL_SSL;
				}
			}

			$uri = $this->make('security')->sanitizeURL($_SERVER['REQUEST_URI']);
			if (strpos($uri, '%7E') !== false) {
				$uri = str_replace('%7E', '~', $uri);
			}

			if (($base_url != $protocol . $_SERVER['HTTP_HOST']) && ($base_url . ':' . $_SERVER['SERVER_PORT'] != 'https://' . $_SERVER['HTTP_HOST'])) {
				header('HTTP/1.1 301 Moved Permanently');  
				header('Location: ' . $base_url . $uri);
				exit;
			}	
		}
	}

	/** 
	 * Turns off the lights.
	 */
	public function shutdown() {
		$db = Database::get();
		if ($db->isConnected()) {
			$db->close();
		}
		if (defined('ENABLE_OVERRIDE_CACHE') && ENABLE_OVERRIDE_CACHE) {
			Environment::saveCachedEnvironmentObject();
		} else if (defined('ENABLE_OVERRIDE_CACHE') && (!ENABLE_OVERRIDE_CACHE)) {
			$env = Environment::get();
			$env->clearOverrideCache();
		}
	}

	/**
	 * Inspects the request and determines what to serve.
	 */
	public function dispatch(Request $request) {
		if ($this->installed) { 
			$response = $this->getEarlyDispatchResponse();
		}
		if (!isset($response)) {
			$collection = Router::getInstance()->getList();
			$router = Router::getInstance();
			$context = new \Symfony\Component\Routing\RequestContext();
			$context->fromRequest($request);
			$matcher = new UrlMatcher($collection, $context);
			$path = rtrim($request->getPathInfo(), '/') . '/';
		    $request->attributes->add($matcher->match($path));
			$matched = $matcher->match($path);
			$route = $collection->get($matched['_route']);
			$router->setRequest($request);
			$response = $router->execute($route, $matched);
		}
		return $response;
	}



}