<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Frontend_Block extends Controller {

	public function action($cID, $bID, $arHandle, $action) {
		$c = Page::getByID($cID, 'ACTIVE');
		if (is_object($c) && !$c->isError()) {
			$cp = new Permissions($c);
			if ($cp->canViewPage()) {
				$a = Area::get($c, $arHandle);
				$b = Block::getByID($bID, $c, $a);
				if (is_object($b) && is_object($a)) {
					$bp = new Permissions($b);
					if ($bp->canViewBlock()) {
						$method = 'action_' . $action;
						$bt = $b->getBlockTypeObject();
						$class = $bt->getBlockTypeClass();
						$bc = new $class($b);
						if (is_callable(array($bc, $method))) {
							$response = call_user_func_array(array($bc, $method), array());
							$request = Request::getInstance();
							$request->setCurrentPage($c);
							$dr = new DispatcherRouteCallback(false);
							return $dr->execute($request);
						}
					}
				}
			}
		}
	}

}

