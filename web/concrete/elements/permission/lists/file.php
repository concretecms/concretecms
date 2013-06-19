<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="clearfix">

<? 

$enablePermissions = false;
if (!$f->overrideFileSetPermissions()) { ?>

	<div class="block-message alert-message notice">
	<p>
	<?=t("Permissions for this file are currently dependent on file sets and global file permissions.")?>
	</p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_setFilePermissionsToOverride()"><?=t('Override Permissions')?></a>
	</div>
	
<? } else { 
	$enablePermissions = true;
	?>

	<div class="block-message alert-message notice">
	<p><?=t("Permissions for this file currently override its sets and the global file permissions.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToGlobalFilePermissions()"><?=t('Revert to File Set and Global Permissions')?></a>
	</div>

<? } ?>


<?=Loader::element('permission/help');?>

<? $cat = PermissionKeyCategory::getByHandle('file');?>

<form method="post" id="ccm-permission-list-form" action="<?=$cat->getToolsURL("save_permission_assignments")?>&fID=<?=$f->getFileID()?>">

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('file');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($f);
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
<div id="ccm-file-permissions-advanced-buttons" style="display: none">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn"><?=t('Cancel')?></a>
	<button onclick="$('#ccm-permission-list-form').submit()" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
</div>
<? } ?>

</div>

<script type="text/javascript">

ccm_permissionLaunchDialog = function(link) {
	jQuery.fn.dialog.open({
		title: $(link).attr('dialog-title'),
		href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file?fID=<?=$f->getFileID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
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
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}		
	});
});

ccm_revertToGlobalFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("revert_to_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_setFilePermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("override_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_refreshFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions?fID=<?=$f->getFileID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		ccm_filePermissionsSetupButtons();
		jQuery.fn.dialog.hideLoader();
	});
}

</script>