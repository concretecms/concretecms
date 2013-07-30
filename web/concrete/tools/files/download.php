<?
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
$fh = Loader::helper('file');
$vh = Loader::helper('validation/identifier');
$form = Loader::helper('form');

$fp = FilePermissions::getGlobal();
if (!$fp->canSearchFileSet()) {
	die(t("Unable to search file sets."));
}

$ci = Loader::helper('file');


if (isset($_REQUEST['fID']) && is_array($_REQUEST['fID'])) {

	// zipem up
	
	$filename = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';
	$files = '';
	$filenames = array();
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID(intval($fID));
		$fp = new Permissions($f);
		if ($fp->canViewFile()) {
			if (!in_array(basename($f->getPath()), $filenames)) {
				$files .= "'" . addslashes($f->getPath()) . "' ";
			}
			$f->trackDownload();
			$filenames[] = basename($f->getPath());
		}
	}
	exec(DIR_FILES_BIN_ZIP . ' -j \'' . addslashes($filename) . '\' ' . $files);
	$ci->forceDownload($filename);	

} else if($_REQUEST['fID']) {
	
	$f = File::getByID(intval($_REQUEST['fID']));
	$fp = new Permissions($f);
	if ($fp->canViewFile()) {
		if (isset($_REQUEST['fvID'])) {
			$fv = $f->getVersion($_REQUEST['fvID']);
		} else {
			$fv = $f->getApprovedVersion();
		}
		$f->trackDownload();
		$ci->forceDownload($fv->getPath());
	}
}