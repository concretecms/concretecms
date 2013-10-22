<?
defined('C5_EXECUTE') or die("Access Denied.");

use \Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;

class Concrete5_Library_Router {

	static $instance = null;
	protected $collection;
	protected $request;
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
	
	public static function getInstance() {
		if (null == self::$instance) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	public function register($rtHandle, $rtPath, $callback) {
		// setup up standard concrete5 routing.
		$rtPath = '/' . trim($rtPath, '/') . '/';
		foreach(array($rtHandle => $rtPath, $rtHandle . '_action' => $rtPath .'{action}/', $rtHandle . '_parameters' => $rtPath . '{action}/{parameters}') as $key => $path) {
			$attributes = array();
			$attributes['path'] = $rtPath;
			if ($callback instanceof Closure) {
				$attributes['callback'] = new ClosureRouteCallback($callback);
			} else if (is_string($callback)) {
				$attributes['callback'] = new ControllerRouteCallback($callback);
			}
			$route = new Route($path, $attributes, array('parameters' => '.+'));
			$this->collection->add($key, $route);
		}
	}

	public function execute(Route $route, $parameters) {
		$callback = $route->getCallback();
		$response = $callback->execute($this->request, $route, $parameters);
		return $response;
	}
}
