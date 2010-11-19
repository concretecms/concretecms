<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$db = Loader::db();
if (isset($_REQUEST['cID']) && is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $displayOrder => $cID) { 
		$v = array($displayOrder, $cID);
		$c = Page::getByID($cID);
		$c->updateDisplayOrder($displayOrder,$cID);
	}
}

$json['error'] = false;
$json['message'] = t("Display order saved.");
$js = Loader::helper('json');
print $js->encode($json);

?>