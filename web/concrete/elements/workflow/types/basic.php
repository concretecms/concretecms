<h3><?=t("Workflow Access")?></h3>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('basic_workflow');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($workflow);
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshBasicWorkflowPermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=Loader::helper('concrete/urls')->getToolsURL('permissions/dialogs/basic_workflow')?>?wfID=<?=$workflow->getWorkflowID()?>&pkID=<?=$pk->getPermissionKeyID()?>"><?=$pk->getPermissionKeyName()?></a></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>

<script type="text/javascript">
$(function() {
	$('.dialog-launch').dialog();
});

ccm_refreshBasicWorkflowPermissions = function() {
	window.location.reload();
}
</script>