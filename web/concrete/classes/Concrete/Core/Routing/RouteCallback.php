<?
namespace Concrete\Core\Routing;

abstract class RouteCallback {

	protected $callback;

	abstract public function execute(\Concrete\Core\Http\Request $request, \Concrete\Core\Routing\Route $route, $parameters);

	abstract public static function getRouteAttributes($callback);

	public function __construct($callback) {
		$this->callback = $callback;
	}
}