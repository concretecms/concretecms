<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');

$resp = array();

$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canRead()) {
	die(_("Access Denied."));
}

$resp['error'] = false;
$resp['filePathDirect'] = $f->getRelativePath();
$resp['filePathInline'] = View::url('/download_file', 'view_inline', $f->getFileID());
$resp['filePath'] = View::url('/download_file', 'view', $f->getFileID());
$resp['title'] = $f->getTitle();
$resp['fileName'] = $f->getFilename();
$resp['thumbnailLevel1'] = $f->getThumbnailSRC(1);
$resp['thumbnailLevel2'] = $f->getThumbnailSRC(2);
$resp['thumbnailLevel3'] = $f->getThumbnailSRC(3);
$resp['width'] = $f->getAttribute("width");
$resp['height'] = $f->getAttribute("height");
$resp['fID'] = $f->getFileID();

$h = Loader::helper('json');
print $h->encode($resp);