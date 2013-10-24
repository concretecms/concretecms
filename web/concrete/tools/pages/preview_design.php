<?

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID'], 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if ($cp->canViewPageVersions()) {
	$req = Request::get();
	$v = new PageView($c);
	if ($_REQUEST['pTemplateID']) {
		$pt = PageTemplate::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['pTemplateID']));
		if (is_object($pt)) {
			$v->setCustomPageTemplate($pt);
		}
	}
	if ($_REQUEST['pThemeID']) {
		$pt = PageTheme::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['pThemeID']));
		if (is_object($pt)) {
			$v->setCustomPageTheme($pt);
		}
	}
	$req->setCustomRequestUser(-1);
	$v->render(); 
}