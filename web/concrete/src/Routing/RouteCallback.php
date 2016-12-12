<?php
namespace Concrete\Core\Routing;
use Loader;
use Request;

abstract class RouteCallback {

	protected $callback;

	abstract public function execute(Request $request, \Concrete\Core\Routing\Route $route, $parameters);

	public function __construct($callback) {
		$this->callback = $callback;
	}
}
