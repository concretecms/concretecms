<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

?>

<div style="text-align: center">
<?

$to = $fv->getTypeObject();
Loader::element('files/edit/' . $to->getEditor(), array('fv' => $fv));

?>
</div>