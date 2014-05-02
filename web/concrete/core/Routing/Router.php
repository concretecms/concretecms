<?php
namespace Concrete\Core\Routing;
use \Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Request;
use Loader;

class Router {

	protected $collection;
	protected $request;
	protected $themePaths = array();
	public $routes = array();

	public function __construct() {
		$this->collection = new SymfonyRouteCollection();
	}

	public function getList() {
		return $this->collection;
	}

	public function setRequest(Request $req) {
		$this->request = $req;
	}

	public function register($rtPath, $callback, $rtHandle = null, $additionalAttributes = array()) {
		// setup up standard concrete5 routing.
		$rtPathTrimmed = trim($rtPath, '/');
		if (!$rtHandle) {
			$rtHandle = preg_replace('/[^A-Za-z0-9\_]/', '_', $rtPathTrimmed);
			$rtHandle = preg_replace('/\_+/', '_', $rtHandle);
			$rtHandle = trim($rtHandle, '_');
		}
		$rtPath = '/' . $rtPathTrimmed . '/';
		$attributes = array();

		if ($callback instanceof \Closure) {
			$attributes = ClosureRouteCallback::getRouteAttributes($callback);
		} else if ($callback == 'dispatcher') {
			$attributes = DispatcherRouteCallback::getRouteAttributes($callback);
		} else {
			$attributes = ControllerRouteCallback::getRouteAttributes($callback);
		}
		$attributes['path'] = $rtPath;
		$route = new Route($rtPath, $attributes, $additionalAttributes);
		$this->collection->add($rtHandle, $route);
	}

	public function execute(Route $route, $parameters) {
		$callback = $route->getCallback();
		$response = $callback->execute($this->request, $route, $parameters);
		return $response;
	}

	/**
	 * Used by the theme_paths and site_theme_paths files in config/ to hard coded certain paths to various themes
	 * @access public
	 * @param $path string
	 * @param $theme object, if null site theme is default
	 * @return void
	*/
	public function setThemeByRoute($path, $theme = NULL, $wrapper = FILENAME_THEMES_VIEW) {
		$this->themePaths[$path] = array($theme, $wrapper);
	}

	/**
	 * This grabs the theme for a particular path, if one exists in the themePaths array
	 * @param string $path
	 * @return string|boolean
	*/
	public function getThemeByRoute($path) {
		// there's probably a more efficient way to do this
		$txt = Loader::helper('text');
		foreach ($this->themePaths as $lp => $layout) {
			if ($txt->fnmatch($lp, $path)) {
				return $layout;
			}
		}
		return false;
	}



}
