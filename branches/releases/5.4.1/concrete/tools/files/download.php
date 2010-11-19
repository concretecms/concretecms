<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
$fh = Loader::helper('file');
$vh = Loader::helper('validation/identifier');
$form = Loader::helper('form');

$fp = FilePermissions::getGlobal();
if (!$fp->canRead()) {
	die(_("Access Denied."));
}

$ci = Loader::helper('file');


if (isset($_REQUEST['fID']) && is_array($_REQUEST['fID'])) {

	// zipem up
	
	$filename = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';
	$files = '';
	$filenames = array();
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canRead()) {
			if (!in_array(basename($f->getPath()), $filenames)) {
				$files .= "'" . addslashes($f->getPath()) . "' ";
			}
			$filenames[] = basename($f->getPath());
		}
	}
	exec(DIR_FILES_BIN_ZIP . ' -j \'' . addslashes($filename) . '\' ' . $files);
	$ci->forceDownload($filename);	

} else {
	
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canRead()) {
		if (isset($_REQUEST['fvID'])) {
			$fv = $f->getVersion($_REQUEST['fvID']);
		} else {
			$fv = $f->getApprovedVersion();
		}
		
		$ci->forceDownload($fv->getPath());
	}
}