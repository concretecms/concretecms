<?

defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if ($cp->canViewPageVersions()) {
	$c->loadVersionObject(Loader::helper('security')->sanitizeInt($_REQUEST['cvID']));
	$req = Request::getInstance();
	$v = new PageView($c);
	$req->setCustomRequestUser(-1);
	$v->render();
}