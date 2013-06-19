<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $cat = PermissionKeyCategory::getByHandle('basic_workflow');?>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('basic_workflow');

foreach($permissions as $pk) { 
	$pk->setPermissionObject($workflow);
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
<? if ($enablePermissions) { ?>

	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/basic_workflow?wfID=<?=$workflow->getWorkflowID()?>&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: false,
			width: 500,
			height: 380
		});		
	}
	</script>
	
<? } ?>
