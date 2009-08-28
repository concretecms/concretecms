<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$ch = Page::getByPath("/dashboard/sitemap");
$chp = new Permissions($ch);
if (!$chp->canRead()) {
	die(_("Access Denied."));
}

$db = Loader::db();
if (isset($_REQUEST['cID']) && is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $displayOrder => $cID) {
		$v = array($displayOrder, $cID);
		$c = Page::getByID($cID);
		$c->updateDisplayOrder($displayOrder);
	}
}

$json['error'] = false;
$json['message'] = t("Display order saved.");
$js = Loader::helper('json');
print $js->encode($json);

?>