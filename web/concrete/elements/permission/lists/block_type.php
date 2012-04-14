<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('block_type');
foreach($permissions as $pk) { 
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshBlockTypePermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/block_type?pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>

	<script type="text/javascript">
	$(function() {
		$('.dialog-launch').dialog();
	});
	ccm_refreshBlockTypePermissions = function() {
		window.location.reload();
	}
	</script>