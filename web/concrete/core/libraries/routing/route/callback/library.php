<?
defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_RouteCallback {

	protected $callback;

	abstract public function execute(Request $request, Route $route, $parameters);

	public function __construct($callback) {
		$this->callback = $callback;
	}
}
