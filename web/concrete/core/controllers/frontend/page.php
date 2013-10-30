<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Frontend_Page extends Controller {

	public function viewVersionStyles($cID, $cvID, $stylesheet) {
		$c = Page::getByID($cID);
		if (is_object($c) && !$c->isError()) {
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) {
				$c->loadVersionObject($cvID);
				$pt = $c->getCollectionThemeObject();
				$values = $c->getCustomThemeStyles();
				$content = $pt->parseStyleSheet($stylesheet, $values);
				$response = new Response();
				$response->headers->set('Content-Type', 'text/css');
				$response->setContent($content);
				return $response;
			}
		}
	}

}

