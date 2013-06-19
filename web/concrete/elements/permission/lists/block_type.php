<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('block_type');

foreach($permissions as $pk) { 
	?>
	<tr>
	<td class="ccm-permission-grid-name" id="ccm-permission-grid-name-<?=$pk->getPermissionKeyID()?>"><strong><a dialog-title="<?=tc('PermissionKeyName', $pk->getPermissionKeyName())?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" href="javascript:void(0)"><?=tc('PermissionKeyName', $pk->getPermissionKeyName())?></a></strong></td>
	<td id="ccm-permission-grid-cell-<?=$pk->getPermissionKeyID()?>" class="ccm-permission-grid-cell"><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>


	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/block_type?pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: false,
			width: 500,
			height: 380
		});		
	}
	</script>
