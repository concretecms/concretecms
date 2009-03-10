<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/files");
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
	$resp = $fv->refreshAttributes();
	switch($resp) {
		case File::F_ERROR_FILE_NOT_FOUND:
			print '<li><div class="ccm-error">' . t('File <strong>%s</strong> could not be found.', $fv->getFilename()) . '</div></li>';
			break;
		default:
		print '<li>';
			print t('File <strong>%s</strong> has been rescanned', $fv->getFileName()) . '</li>';
			break;
	}
}
print '</ol>';

