<?

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID'], 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if ($cp->canViewPageVersions()) {
	$req = Request::getInstance();
	$req->setCurrentPage($c);
	$controller = Loader::controller($c);
	$view = $controller->getViewObject();
	if ($_REQUEST['pTemplateID']) {
		$pt = PageTemplate::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['pTemplateID']));
		if (is_object($pt)) {
			$view->setCustomPageTemplate($pt);
		}
	}
	if ($_REQUEST['pThemeID']) {
		$pt = PageTheme::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['pThemeID']));
		if (is_object($pt)) {
			$view->setCustomPageTheme($pt);
		}
	}
	$req->setCustomRequestUser(-1);
	$response = new Response();
	$content = $view->render();
	$response->setContent($content);
	$response->send();
}