<?
defined('C5_EXECUTE') or die("Access Denied.");

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;


class Concrete5_Library_Dispatcher {

	static $dispatcher = null;

	protected function setRequest(Request $r) {
		$this->request = $r;
	}

	public static function get(Request $request) {
		if (null === self::$dispatcher) {
			self::$dispatcher = new Dispatcher();
			self::$dispatcher->setRequest($request);
		}
		return self::$dispatcher;
	}

	public function dispatch() {
		$collection = Router::getInstance()->getList();
		$router = Router::getInstance();

		$context = new RequestContext($this->request);
		$matcher = new UrlMatcher($collection, $context);
		$matched = $matcher->match($this->request->getPathInfo());
		$route = $collection->get($matched['_route']);

		$router->setRequest($this->request);
		$response = $router->execute($route, $matched);
		return $response;
	}


}