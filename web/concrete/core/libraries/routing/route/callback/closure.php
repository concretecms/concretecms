<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ClosureRouteCallback extends RouteCallback {

	public function execute(Request $request, Route $route, $parameters) {
		$r = new Response();
		$r->setContent($this->callback->__invoke());
		return $r;
	}


}
