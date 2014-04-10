<?
namespace Concrete\Core\Routing;
use Response;
class ClosureRouteCallback extends RouteCallback {

	public function execute(\Concrete\Core\Http\Request $request, \Concrete\Core\Routing\Route $route, $parameters) {
		$r = new Response();
		$r->setContent($this->callback->__invoke());
		return $r;
	}

	public static function getRouteAttributes($callback) {
		$callback = new static($callback);
		return array('callback' => $callback);
	}


}
