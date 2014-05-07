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


if (isset($_REQUEST['item']) && is_array($_REQUEST['item'])) {

	// zipem up
	
	$filename = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';
	$files = array();
	$filenames = array();
	foreach($_REQUEST['item'] as $fID) {
		$f = File::getByID(intval($fID));
		if($f->isError()) {
			continue;
		}
		$fp = new Permissions($f);
		if ($fp->canViewFile()) {
			if (!in_array(basename($f->getPath()), $filenames)) {
				$files[] = $f->getPath();
			}
			$f->trackDownload();
			$filenames[] = basename($f->getPath());
		}
	}
	if(empty($files)) {
		die(t("None of the requested files could be found."));
	}
	if(class_exists('ZipArchive', false)) {
		$zip = new ZipArchive;
		$res = $zip->open($filename, ZipArchive::CREATE);
		if($res !== true) {
			throw new Exception(t('Could not open with ZipArchive::CREATE'));
		}
		foreach($files as $f) {
			$zip->addFile($f, basename($f));
		}
		$zip->close();
	}
	else {
		$exec = escapeshellarg(DIR_FILES_BIN_ZIP) . ' -j ' . escapeshellarg($filename);
		foreach($files as $f) {
			$exec .= ' ' . escapeshellarg($f);
		}
		$exec .= ' 2>&1';
		@exec($exec, $output, $rc);
		if($rc !== 0) {
			throw new Exception(t('External zip failed. Error description: %s', implode("\n", $outout)));
		}
	}
	$ci->forceDownload($filename);

} else if($_REQUEST['fID']) {
	
	$f = File::getByID(intval($_REQUEST['fID']));
	if($f->isError()) {
		switch($f->getError()) {
			case File::F_ERROR_FILE_NOT_FOUND:
				die(t("The requested file couldn't be found."));
			case File::F_ERROR_INVALID_FILE:
				die(t("The requested file is not valid."));
			default:
				die(t("An unexpected error occurred while looking for the requested file"));
		}
	}
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