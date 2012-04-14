<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<table class="ccm-permission-grid">
<?
Loader::model('file_set');
$permissions = PermissionKey::getList('file_set');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($fs);
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshFileSetPermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?fsID=<?=$fs->getFileSetID()?>&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>

	<script type="text/javascript">
	$(function() {
		$('.dialog-launch').dialog();
	});
	ccm_refreshFileSetPermissions = function() {
		window.location.reload();
	}
	</script>