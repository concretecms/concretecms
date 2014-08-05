<?
namespace Concrete\Core\Routing;
use Response;
use Request;
class ClosureRouteCallback extends RouteCallback {

	public function execute(Request $request, \Concrete\Core\Routing\Route $route, $parameters) {
		$r = new Response();
		$r->setContent($this->callback->__invoke());
		return $r;
	}

	public function __sleep() {
		unset($this->callback);
	}

	public static function getRouteAttributes($callback) {
		$callback = new static($callback);
		return array('callback' => $callback);
	}


}
