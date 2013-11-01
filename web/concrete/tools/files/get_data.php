<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}

$resp = array();

$fileIDs = array();
$files = array();
if (is_array($_REQUEST['fID'])) {
	$fileIDs = $_REQUEST['fID'];
} else {
	$fileIDs[] = $_REQUEST['fID'];
}

foreach($fileIDs as $fID) {
	$f = File::getByID($fID);
	$fp = new Permissions($f);
	if ($fp->canViewFileInFileManager()) {
		$files[] = $f;
	}
}

if (count($files) == 0) {
	die(t("Access Denied."));
}

$i = 0;
foreach($files as $f) {
	$ats = $f->getAttributeList();
	$resp[$i]['error'] = false;
	$resp[$i]['filePathDirect'] = $f->getRelativePath();
	$resp[$i]['filePathInline'] = View::url('/download_file', 'view_inline', $f->getFileID());
	$resp[$i]['filePath'] = View::url('/download_file', 'view', $f->getFileID());
	$resp[$i]['title'] = $f->getTitle();
	$resp[$i]['description'] = $f->getDescription();
	$resp[$i]['fileName'] = $f->getFilename();
	$resp[$i]['thumbnailLevel1'] = $f->getThumbnailSRC(1);
	$resp[$i]['thumbnailLevel2'] = $f->getThumbnailSRC(2);
	$resp[$i]['thumbnailLevel3'] = $f->getThumbnailSRC(3);
	$resp[$i]['fID'] = $f->getFileID();
	foreach($ats as $key => $value) {
		$resp[$i][$key] = $value;
	}
	$i++;
}

$h = Loader::helper('json');
print $h->encode($resp);