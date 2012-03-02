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
	<p><?=t("Permissions for this file currently override those of the area and page.")?></p>
	<br/>
	<a href="javascript:void(0)" class="btn small" onclick="ccm_revertToGlobalFilePermissions()"><?=t('Revert to File Set and Global Permissions')?></a>
	</div>

<? } ?>


<?=Loader::element('permission/help');?>
<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('file');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($f);
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><? if ($enablePermissions) { ?><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshFilePermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file?fID=<?=$f->getFileID()?>&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($enablePermissions) { ?></a><? } ?></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>
</div>

<script type="text/javascript">
ccm_revertToGlobalFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("revert_to_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_setFilePermissionsToOverride = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=$pk->getPermissionKeyToolsURL("override_global_file_permissions")?>&fID=<?=$f->getFileID()?>', function() { 
		ccm_refreshFilePermissions();
	});
}

ccm_refreshFilePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions?fID=<?=$f->getFileID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});
}

</script>