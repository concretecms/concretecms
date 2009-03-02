<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

$resp = array();
//$resp['error'] = true;
//$resp['message'] = 'Heyo';

$f = File::getByID($_REQUEST['fID']);

$resp['error'] = false;
$resp['filePathDirect'] = $f->getRelativePath();
$resp['filePathInline'] = View::url('/download_file', 'view_inline', $f->getFileID());
$resp['filePath'] = View::url('/download_file', 'view', $f->getFileID());
$resp['title'] = $f->getTitle();
$resp['fileName'] = $f->getFilename();
$resp['width'] = $f->getAttribute("width");
$resp['height'] = $f->getAttribute("height");
$resp['fID'] = $f->getFileID();

$h = Loader::helper('json');
print $h->encode($resp);