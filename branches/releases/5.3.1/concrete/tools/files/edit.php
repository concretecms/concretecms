<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');

$ci = Loader::helper('concrete/urls');
$f = File::getByID($_REQUEST['fID']);
$fv = $f->getApprovedVersion();

$fp = new Permissions($f);
if (!$fp->canWrite()) {
	die(_("Access Denied."));
}


$to = $fv->getTypeObject();

$url = $ci->getToolsURL('files/edit/' . $to->getEditor()) . '?fID=' . $_REQUEST['fID'];

?>
<iframe class="ccm-file-editor-wrapper" id="ccm-file-editor-wrapper<?php echo time()?>" style="padding: 0px; border: 0px; margin: 0px" width="100%" height="100%" frameborder="0" border="0" src="<?php echo $url?>"></iframe>