<?
defined('C5_EXECUTE') or die("Access Denied.");

function shutdownRescan() {
	$isError = false;
	global $fv;
	$error = error_get_last();
	if ($error != false) {
		if ($error['type'] == E_ERROR) {
			print '<li><div class="ccm-error">' . t('Unable to rescan %s. Error encountered: %s. Rescan halted.', $fv->getTitle(), $error['message']) . '</div></li>';
		}
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
register_shutdown_function('shutdownRescan');

$u = new User();
$form = Loader::helper('form');

print '<ol>';
$fcnt = 0;
if(is_array($_REQUEST['fID'])) foreach($_REQUEST['fID'] as $fID) {
	$f = File::getByID($fID);
	$fp = new Permissions($f);
	if ($fp->canEditFileContents()) {
		$fcnt++;
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
}
print '</ol>';

if ($fcnt == 0) { ?>
	<?=t('You do not have permission to rescan any of the selected files.'); ?>
<? } ?>
