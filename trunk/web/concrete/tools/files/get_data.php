<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');

$respw = array();

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
	if ($fp->canRead()) {
		$files[] = $f;
	}
}

if (count($files) == 0) {
	die(_("Access Denied."));
}

$i = 0;
foreach($files as $f) {
	$resp[$i]['error'] = false;
	$resp[$i]['filePathDirect'] = $f->getRelativePath();
	$resp[$i]['filePathInline'] = View::url('/download_file', 'view_inline', $f->getFileID());
	$resp[$i]['filePath'] = View::url('/download_file', 'view', $f->getFileID());
	$resp[$i]['title'] = $f->getTitle();
	$resp[$i]['fileName'] = $f->getFilename();
	$resp[$i]['thumbnailLevel1'] = $f->getThumbnailSRC(1);
	$resp[$i]['thumbnailLevel2'] = $f->getThumbnailSRC(2);
	$resp[$i]['thumbnailLevel3'] = $f->getThumbnailSRC(3);
	$resp[$i]['width'] = $f->getAttribute("width");
	$resp[$i]['height'] = $f->getAttribute("height");
	$resp[$i]['fID'] = $f->getFileID();
	$i++;
}

$h = Loader::helper('json');
print $h->encode($resp);