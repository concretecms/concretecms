<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_DispatcherRouteCallback extends RouteCallback {
	
	protected function sendResponse(View $v, $code = 200) {
		$contents = $v->render();
		$response = new Response($contents, $code);
		return $response;
	}

	protected function sendPageNotFound() {
		$cnt = Loader::controller('/page_not_found');
		$v = $cnt->getViewObject();
		$cnt->on_start();
		$cnt->runAction('view');
		$v->setController($cnt);
		return $this->sendResponse($v, 404);
	}

	protected function sendPageForbidden() {
		$cnt = Loader::controller('/page_forbidden');
		$v = $cnt->getViewObject();
		$cnt->on_start();
		$cnt->runAction('view');
		$v->setController($cnt);
		return $this->sendResponse($v, 403);
	}

	public function execute(Request $request, Route $route, $parameters) {
		
		// figure out where we need to go
		$c = Page::getFromRequest($request);
		if ($c->isError() && $c->getError() == COLLECTION_NOT_FOUND) {
			$this->sendPageNotFound();
		}
		// maintenance mode
		if ((!$c->isAdminArea()) && ($c->getCollectionPath() != '/login')) {
			$smm = Config::get('SITE_MAINTENANCE_MODE');
			if ($smm == 1 && ($_SERVER['REQUEST_METHOD'] != 'POST' || Loader::helper('validation/token')->validate() == false)) {
				$cnt = Loader::controller('/maintenance_mode');
				$v = $cnt->getViewObject();
				return $this->sendResponse($v);
			}
		}

		// Check to see whether this is an external alias or a header 301 redirect. If so we go there.
		/*
		if (($request->getPath() != '') && ($request->getPath() != $c->getCollectionPath())) {
			// canonnical paths do not match requested path
			return Redirect::page($c, 301);
		}
		*/

		if ($c->getCollectionPointerExternalLink() != '') {
			return Redirect::go($c->getCollectionPointerExternalLink());
		}

		$cp = new Permissions($c);
		if ($cp->isError() && $cp->getError() == COLLECTION_FORBIDDEN) {
			$this->sendPageForbidden();
		}

		if (!$c->isActive() && (!$cp->canViewPageVersions())) {
			$this->sendPageNotFound();
		}

		if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canViewPageVersions()) {
			$c->loadVersionObject('RECENT');
		}

		$vp = new Permissions($c->getVersionObject());

		// returns the $vp object, which we then check
		if (is_object($vp) && $vp->isError()) {
			switch($vp->getError()) {
				case COLLECTION_NOT_FOUND:
					$this->sendPageNotFound();
					break;
				case COLLECTION_FORBIDDEN:
					$this->sendPageForbidden();
					break;
			}
		}

		$request->setCurrentPage($c);
		require(DIR_BASE_CORE . '/startup/process.php');
		$u = new User();
		if (STATISTICS_TRACK_PAGE_VIEWS == 1) {
			$u->recordView($c);
		}

		## Fire the on_page_view Eventclass
		Events::fire('on_page_view', $c, $u);

		$controller = Loader::controller($c);
		$controller->on_start();
		$controller->setupRequestActionAndParameters($request);
		$requestTask = $controller->getRequestAction();
		$requestParameters = $controller->getRequestActionParameters();
		if (!$controller->validateRequest()) {
			return $this->sendPageNotFound();
		}
		$controller->runAction($requestTask, $requestParameters);
		$view = $controller->getViewObject();
		return $this->sendResponse($view);
	}

	public static function getRouteAttributes($callback) {
		$callback = new DispatcherRouteCallback($callback);
		return array('callback' => $callback);
	}


}
