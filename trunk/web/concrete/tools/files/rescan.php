<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

print '<ol>';
foreach($_REQUEST['fID'] as $fID) {
	$f = File::getByID($fID);
	$fv = $f->getApprovedVersion();
	$fv->refreshAttributes();
	print '<li>';
	print t('File <strong>%s</strong> has been rescanned', $fv->getFileName()) . '</li>';
}
print '</ol>';

