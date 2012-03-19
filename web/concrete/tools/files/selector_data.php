<?
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Access Denied."));
}



$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canRead()) {
	die(t("Access Denied."));
}

$fv = $f->getApprovedVersion();

$canViewInline = $fv->canView() ? 1 : 0;
$canEdit = $fv->canEdit() ? 1 : 0;
?>

<div class="ccm-file-selected" fID="<?=$_REQUEST['fID']?>" ccm-file-manager-field="<?=$_REQUEST['ccm_file_selected_field']?>" ccm-file-manager-can-admin="<?=($fp->canAdmin())?>" ccm-file-manager-can-delete="<?=$fp->canAdmin()?>" ccm-file-manager-can-view="<?=$canViewInline?>" ccm-file-manager-can-replace="<?=$fp->canWrite()?>" ccm-file-manager-can-edit="<?=$canEdit?>"  >
<div class="ccm-file-selected-thumbnail"><?=$fv->getThumbnail(1)?></div>
<div class="ccm-file-selected-data"><div><?=$fv->getTitle()?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>