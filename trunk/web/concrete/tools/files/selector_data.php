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

$canViewInline = $fv->canView() ? 1 : 0;
$canEdit = $fv->canEdit() ? 1 : 0;
?>

<div class="ccm-file-selected" fID="<?=$_REQUEST['fID']?>" ccm-file-manager-field="<?=$_REQUEST['ccm_file_selected_field']?>" ccm-file-manager-can-view="<?=$canViewInline?>" ccm-file-manager-can-edit="<?=$canEdit?>" >
<div class="ccm-file-selected-thumbnail"><?=$fv->getThumbnail(1)?></div>
<div class="ccm-file-selected-data"><div><?=$fv->getTitle()?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>