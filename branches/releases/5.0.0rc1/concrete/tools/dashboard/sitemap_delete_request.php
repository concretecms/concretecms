<?php 
// this should be cleaned up.... yeah

$u = new User();

$json = array();
$json['error'] = false;
$json['message'] = false;


if (isset($_REQUEST['cID'] ) && is_numeric($_REQUEST['cID'])) {
	$c = Page::getByID($_REQUEST['cID']);
} else {
	$error = 'Invalid ID passed';
}

if ((!is_object($c)) || (($c->getCollectionID() != $_REQUEST['cID']) && ($c->getCollectionPointerOriginalID() != $_REQUEST['cID']))) {
	$error = 'Invalid Collection Specified';
}

$cp = new Permissions($c);
if (!$cp->canDeleteCollection()) {
	$error = 'You are not allowed to delete this collection';
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
	$json['message'] = 'Page Removed';
}

print json_encode($json);
exit;

?>