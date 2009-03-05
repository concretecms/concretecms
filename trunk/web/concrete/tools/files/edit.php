<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
$form = Loader::helper('form');
if (!$cp->canRead()) {
	die(_("Access Denied."));
}

$ci = Loader::helper('concrete/urls');
$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

$to = $fv->getTypeObject();

$url = $ci->getToolsURL('files/edit/' . $to->getEditor()) . '?fID=' . $_REQUEST['fID'];

?>
<iframe class="ccm-file-editor-wrapper" id="ccm-file-editor-wrapper<?=time()?>" style="padding: 0px; border: 0px; margin: 0px" width="100%" height="100%" frameborder="0" border="0" src="<?=$url?>"></iframe>