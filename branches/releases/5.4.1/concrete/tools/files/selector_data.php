<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
$form = Loader::helper('form');


$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canRead()) {
	die(_("Access Denied."));
}

$fv = $f->getApprovedVersion();

$canViewInline = $fv->canView() ? 1 : 0;
$canEdit = $fv->canEdit() ? 1 : 0;
?>

<div class="ccm-file-selected" fID="<?php echo $_REQUEST['fID']?>" ccm-file-manager-field="<?php echo $_REQUEST['ccm_file_selected_field']?>" ccm-file-manager-can-admin="<?php echo ($fp->canAdmin())?>" ccm-file-manager-can-delete="<?php echo $fp->canAdmin()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $fp->canWrite()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>"  >
<div class="ccm-file-selected-thumbnail"><?php echo $fv->getThumbnail(1)?></div>
<div class="ccm-file-selected-data"><div><?php echo $fv->getTitle()?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>