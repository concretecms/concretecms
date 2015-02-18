<?php
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
            $files[] = $f;
			$f->trackDownload();
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
			$zip->addFromString($f->getFilename(), $f->getFileContents());
		}
		$zip->close();
        $ci->forceDownload($filename);
	} else {
	    throw new Exception('Unable to zip files using ZipArchive. Please ensure the Zip extension is installed.');
	}

} else if($_REQUEST['fID']) {
	
	$f = File::getByID(intval($_REQUEST['fID']));
	if($f->isError()) {
		switch($f->getError()) {
            case \Concrete\Core\File\Importer::E_FILE_INVALID:
				die(t("The requested file couldn't be found."));
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
        $f->forceDownload();
	}
}