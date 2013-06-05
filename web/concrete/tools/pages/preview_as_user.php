<?

defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID'], 'RECENT'); //,"ACTIVE"
$cp = new Permissions($c);
if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') {
	$v = View::getInstance();
	$v->disableEditing();
	$v->disableLinks();
	$req = Request::get();
	$req->setCustomRequestUser(false);				
	if (isset($_REQUEST['customUser'])) {
		$ui = UserInfo::getByUserName($_REQUEST['customUser']);
		if (is_object($ui)) {
			$req->setCustomRequestUser($ui->getUserObject());
		}
	}
	$dt = Loader::helper('form/date_time');
	$date = $dt->translate('onDate', $_REQUEST);
	$req->setCustomRequestDateTime($date);
	$req = Request::get();
	$cp = new Permissions($c);
	if ($cp->canRead()) { 
		$v->render($c); 
	} else {
		print t('Unable to view page.');
	}
}