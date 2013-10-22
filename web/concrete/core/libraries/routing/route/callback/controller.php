<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_ControllerRouteCallback extends RouteCallback {

	protected $parameters = array();
	protected $action = false;

	protected function prepareExecute(RequestController $controller, $parameters) {
		$p = trim($parameters['parameters'], '/');
		if ($p) {
			$p = explode('/', $p);
		} else {
			$p = array();
		}
		if ($parameters['action']) {
			try {
				$r = new ReflectionMethod(get_class($controller), $parameters['action']);
				$cl = $r->getDeclaringClass();
				if (is_object($cl)) {
					if ($cl->getName() != 'Controller' 
					&& strpos($method, 'on_') !== 0
					&& strpos($method, '__') !== 0
					&& $r->isPublic()
					&& count($p) == $r->getNumberOfParameters()) {
						$this->action = $parameters['action'];
						$this->parameters = $p;
					} else {
						throw new InvalidControllerArgumentException();
					}
				}
			} catch(Exception $e) {
				throw new InvalidControllerArgumentException();
			}
		} else if (is_callable(array($this->callback, 'view'))) {
			$this->action = 'view';
		}
	}

	protected function getControllerActionParameters($parameters) {
		return explode('/', $p);
	}

	public function execute(Request $request, Route $route, $parameters) {
		$controller = $this->callback;
		// now that we have the controller, we figure out
		// if we ought to
		try {
			$controller = new $controller($route, $request);
			$this->prepareExecute($controller, $parameters);
			$controller->on_start();
			$controller->runAction($this->action, $this->parameters);
			$view = $controller->getView();
			if (isset($view) && $view instanceof View) {
				$response = $view->render();
			}
		} catch(InvalidControllerArgumentException $e) {
			$v = new RequestView();
			return $v->render('/page_not_found');
		}
	}

}