<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
	<? 
	global $c;
	global $a;

$enablePermissions = false;
if (!$b->overrideAreaPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?=t("Permissions for this block are currently dependent on the area containing this block.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setBlockPermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<? } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?=t("Permissions for this block currently override those of the area and page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToAreaPermissions()"><?=t('Revert to Area Permissions')?></a>
	</div>

<? } ?>


<?=Loader::element('permission/help');?>

<? $cat = PermissionKeyCategory::getByHandle('block');?>
<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>">

<table class="ccm-permission-grid">

<?
$permissions = PermissionKey::getList('block');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($b);

?>
<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><? if ($enablePermissions) { ?><a dialog-title="<?=tc('PermissionKeyName', $pk->getPermissionKeyName())?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><? } ?><?=tc('PermissionKeyName', $pk->getPermissionKeyName())?><? if ($enablePermissions) { ?></a><? } ?></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" <? if ($enablePermissions) { ?>class="ccm-permission-grid-cell"<? } ?>><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
<? if ($enablePermissions) { ?>
<tr>
	<td class="ccm-permission-grid-name" ></td>
	<td>
	<?=Loader::element('permission/clipboard', array('pkCategory' => $cat))?>
	</td>
</tr>
<? } ?>

</table>
</form>

<? if ($enablePermissions) { ?>
<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<? } ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?bID=<?=$b->getBlockID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>&btask=set_advanced_permissions&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
		modal: false,
		width: 500,
		height: 380
	});		
}

$(function() {
	$('#ccm-permission-list-form').ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		
		success: function(r) {
			ccm_mainNavDisableDirectExit();
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToAreaPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_mainNavDisableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_setBlockPermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_area_permissions")?>&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function() { 
		ccm_mainNavDisableDirectExit();
		ccm_refreshBlockPermissions();
	});
}

ccm_refreshBlockPermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=groups&bID=<?=$b->getBlockID()?>&cvID=<?=$c->getVersionID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cID=<?=$c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>