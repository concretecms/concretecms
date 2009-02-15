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

<div class="ccm-file-selected">
<div class="ccm-file-selected-thumbnail"><?=$fv->getThumbnail(1)?></div>
<div class="ccm-file-selected-data"><div><?=$fv->getTitle()?></div><div><a href="javascript:void(0)" class="ccm-file-manager-clear-asset" ccm-file-manager-field="<?=$_REQUEST['ccm_file_selected_field']?>"><?=t('Clear')?></a></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>