<?
namespace Concrete\Core\Routing;
use Loader;

abstract class RouteCallback {

	protected $callback;

	abstract public function execute(\Concrete\Core\Http\Request $request, \Concrete\Core\Routing\Route $route, $parameters);

	public function __construct($callback) {
		$this->callback = $callback;
	}
}