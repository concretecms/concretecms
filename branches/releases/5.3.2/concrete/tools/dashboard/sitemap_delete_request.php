<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$ch = Page::getByPath("/dashboard/sitemap");
$chp = new Permissions($ch);
if (!$chp->canRead()) {
	die(_("Access Denied."));
}

$valt = Loader::helper('validation/token');

$u = new User();

$json = array();
$json['error'] = false;
$json['message'] = false;

if ($valt->validate()) {
	
	if (isset($_REQUEST['cID'] ) && is_numeric($_REQUEST['cID'])) {
		$c = Page::getByID($_REQUEST['cID']);
	} else {
		$error = t('Invalid ID passed');
	}
	
	if ((!is_object($c)) || (($c->getCollectionID() != $_REQUEST['cID']) && ($c->getCollectionPointerOriginalID() != $_REQUEST['cID']))) {
		$error = t('Invalid Page ID Specified.');
	}
	
	$cp = new Permissions($c);
	if (!$cp->canDeleteCollection()) {
		$error = t('You are not allowed to delete this page.');
	}

} else {
	$error = $valt->getErrorMessage();
}

if (isset($error)) {
	$json['cID'] = $_REQUEST['cID'];
	$json['error'] = true;
	$json['message'] = $error;
} else {
	if ($c->isAlias()) {
		$c->removeThisAlias();
	} else {
		$c->delete();
	}
	$json['cID'] = $_REQUEST['cID'];
	$json['error'] = false;
	$json['message'] = t('Page Removed');
}

$js = Loader::helper('json');
print $js->encode($json);
exit;