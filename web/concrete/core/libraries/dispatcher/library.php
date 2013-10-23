<?
defined('C5_EXECUTE') or die("Access Denied.");

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;


class Concrete5_Library_Dispatcher {

	static $dispatcher = null;
	protected $installed = false;

	protected function setRequest(Request $r) {
		$this->request = $r;
	}

	/** 
	 * Loads everything in the proper order to begin a concrete5 visit
	 */
	public function bootstrap() {
		require(DIR_BASE_CORE . '/startup/autoload.php');
		if (file_exists(DIR_CONFIG_SITE . '/site_post_autoload.php')) {
			require(DIR_CONFIG_SITE . '/site_post_autoload.php');
		}
		require(DIR_BASE_CORE . '/startup/file_permission_config.php');
		require(DIR_BASE_CORE . '/startup/magic_quotes_gpc_check.php');

		require(DIR_BASE_CORE . '/startup/timezone.php');
		require(DIR_BASE_CORE . '/startup/file_access_check.php');
		require(DIR_BASE_CORE . '/startup/localization.php');
		require(DIR_BASE_CORE . '/config/theme_paths.php');
		if (file_exists(DIR_CONFIG_SITE . '/site_assets.php')) {
			require(DIR_CONFIG_SITE . '/site_assets.php');
		}
		require(DIR_BASE_CORE . '/config/assets.php');
		require(DIR_BASE_CORE . '/startup/session.php');
		require(DIR_BASE_CORE . '/config/routes.php');
		if (file_exists(DIR_CONFIG_SITE . '/routes.php')) {
			require(DIR_CONFIG_SITE . '/routes.php');
		}
		$this->installed = require(DIR_BASE_CORE . '/startup/config_check.php');
		if (!$this->installed) {
			require(DIR_BASE_CORE . '/startup/check_page_cache.php');
		}
		Loader::database();
		if (!$this->installed) {
			require(DIR_BASE_CORE . '/config/app.php');
		}
		require(DIR_BASE_CORE . '/startup/url_check.php');
		require(DIR_BASE_CORE . '/startup/encoding_check.php');
	}

	public function start(Request $request) {
		$cdir = DIR_BASE_CORE;
		$this->setRequest($request);
		if (!$this->installed) {
			if (!$this->request->matches('/install/*') && $this->request->getPath() != '/install') {
				Redirect::send('/install');
			}
		} else {
			require($cdir . '/startup/permission_cache_check.php');
			require($cdir . '/config/localization.php');
			require($cdir . '/startup/security.php');
			require($cdir . '/startup/packages.php');
			require($cdir . '/config/file_types.php');
			require($cdir . '/startup/debug_logging.php');
			if (file_exists(DIR_CONFIG_SITE . '/site_post.php')) {
				require(DIR_CONFIG_SITE . '/site_post.php');
			}

			## Site-level config POST user/app config - managed by c5, do NOT add your own stuff here ##
			if (file_exists(DIR_CONFIG_SITE . '/site_post_restricted.php')) {
				require(DIR_CONFIG_SITE . '/site_post_restricted.php');
			}

			## Specific site routes for various content items (if they exist) ##
			if (file_exists(DIR_CONFIG_SITE . '/site_theme_paths.php')) {
				@include(DIR_CONFIG_SITE . '/site_theme_paths.php');
			}
			if (file_exists(DIR_CONFIG_SITE . '/site_file_types.php')) {
				@include(DIR_CONFIG_SITE . '/site_file_types.php');
			}

			PermissionKey::loadAll();

			require($cdir . '/startup/optional_menu_buttons.php');
			if (defined('ENABLE_APPLICATION_EVENTS') && ENABLE_APPLICATION_EVENTS == true &&  file_exists(DIR_CONFIG_SITE . '/site_events.php')) {
				@include(DIR_CONFIG_SITE . '/site_events.php');
			}
			require($cdir . '/startup/user.php');

		}
	}
	
	public static function get() {
		if (null === self::$dispatcher) {
			self::$dispatcher = new Dispatcher();
		}
		return self::$dispatcher;
	}

	public function dispatch() {
		$collection = Router::getInstance()->getList();
		$router = Router::getInstance();
		$context = new RequestContext();
		$context->fromRequest($this->request);
		$matcher = new UrlMatcher($collection, $context);
	    $this->request->attributes->add($matcher->match($this->request->getPathInfo()));
		$matched = $matcher->match($this->request->getPathInfo());
		$route = $collection->get($matched['_route']);
		$router->setRequest($this->request);
		$response = $router->execute($route, $matched);
		return $response;
	}

	public function shutdown() {
		$db = Loader::db(false, false, false, false, false, false);
		if (is_object($db)) {
			$db->disconnect();
		}
		if (defined('ENABLE_OVERRIDE_CACHE') && ENABLE_OVERRIDE_CACHE) {
			Environment::saveCachedEnvironmentObject();
		} else if (defined('ENABLE_OVERRIDE_CACHE') && (!ENABLE_OVERRIDE_CACHE)) {
			$env = Environment::get();
			$env->clearOverrideCache();
		}
	}


}