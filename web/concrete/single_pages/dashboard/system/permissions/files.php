
	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Manager Permissions'), $help, 'span12 offset2')?>

	<table class="ccm-permission-grid">
	<?
	Loader::model('file_set');
	$fs = FileSet::getGlobal();
	$permissions = PermissionKey::getList('file_set');
	foreach($permissions as $pk) { 
		$pk->setPermissionObject($fs);
		?>
		<tr>
		<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshFileSetPermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?fsID=0&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a></td>
		<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
	</tr>
	<? } ?>
	</table>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	<script type="text/javascript">
	$(function() {
		$('.dialog-launch').dialog();
	});
	ccm_refreshFileSetPermissions = function() {
		window.location.reload();
	}
	</script>