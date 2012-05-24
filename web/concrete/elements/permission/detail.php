<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? 
if ($_REQUEST['paID'] && $_REQUEST['paID'] > 0) { 
	$pa = PermissionAccess::getByID($_REQUEST['paID']);
	if ($pa->isPermissionAccessInUse()) {
		$pa = $pa->duplicate();
	}
} else { 
	$pa = PermissionAccess::create();
}
?>

<div class="ccm-ui" id="ccm-permission-detail">
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post" action="<?=$permissionKey->getPermissionKeyToolsURL()?>">

<input type="hidden" name="paID" value="<?=$pa->getPermissionAccessID()?>" />

<? $workflows = Workflow::getList();?>

<? Loader::element('permission/message_list'); ?>

<? if ($permissionKey->hasCustomOptionsForm() || ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0)) { ?>
	<ul class="tabs" id="ccm-permission-detail-tabs">
		<li class="active"><a href="#" data-tab="access-types"><?=t('Access')?></a></li>
		<? if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) { ?><li><a href="#" data-tab="workflow"><?=t('Workflow')?></a><? } ?></li>
		<? if ($permissionKey->hasCustomOptionsForm()) { ?><li><a href="#" data-tab="custom-options"><?=t('Details')?></a><? } ?></li>
	</ul>
<div class="clearfix"></div>
<? } ?>
	
<? if ($permissionKey->getPermissionKeyDescription()) { ?>
<div class="dialog-help">
<?=$permissionKey->getPermissionKeyDescription()?>
</div>
<? } ?>


<div id="ccm-permission-access-types">
<?
$accessTypes = $permissionKey->getSupportedAccessTypes();
Loader::element('permission/access/list', array('permissionAccess' => $pa, 'accessTypes' => $accessTypes)); ?>
</div>

<? if ($permissionKey->hasCustomOptionsForm()) { ?>
<div id="ccm-permission-custom-options" style="display: none">

<? if ($permissionKey->getPackageID() > 0) { ?>
	<? Loader::packageElement('permission/keys/' . $permissionKey->getPermissionKeyHandle(), $permissionKey->getPackageHandle(), array('permissionKey' => $permissionKey)); ?>
<? } else { ?>
	<? Loader::element('permission/keys/' . $permissionKey->getPermissionKeyHandle(), array('permissionKey' => $permissionKey)); ?>
<? } ?>

</div>

<? } ?>

<? if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) { ?>
	<?
	$selectedWorkflows = $permissionKey->getWorkflows();
	$workflowIDs = array();
	foreach($selectedWorkflows as $swf) {
		$workflowIDs[] = $swf->getWorkflowID();
	}
	?>
		
	<div id="ccm-permission-workflow" style="display: none">
			<h3><?=t('Attach a workflow to this permission?')?></h3>
			<div class="clearfix">
			<label><?=t('Workflow')?></label>
			<div class="input">
			<ul class="inputs-list">
				<? foreach($workflows as $wf) { ?>
					<li><label><input type="checkbox" name="wfID[]" value="<?=$wf->getWorkflowID()?>" <? if (in_array($wf->getWorkflowID(), $workflowIDs)) { ?> checked="checked" <? } ?> /> <span><?=$wf->getWorkflowName()?></span></label></li>
				<? } ?>
			</ul>
			</div>
			</div>
	</div>
<? } ?>

	<div class="dialog-buttons">
		<a href="javascript:void(0)" class="btn" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></a>
		<button type="submit" class="btn primary ccm-button-right" class="btn primary" onclick="$('#ccm-permissions-detail-form').submit()"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
</form>
</div>

<script type="text/javascript">
$(function() {
/*
	$('#ccm-permission-detail-tabs a').unbind().click(function() {
		$('#ccm-permission-detail-tabs li').removeClass('active');
		$(this).parent().addClass('active');
		$('#ccm-permission-access-types').hide();
		$('#ccm-permission-custom-options').hide();
		$('#ccm-permission-workflow').hide();
		var tab = $(this).attr('data-tab');
		$('#ccm-permission-' + tab).show();
		$("#ccm-permission-detail").closest('.ui-dialog-content').jqdialog('option', 'buttons', false);
		switch(tab) {
			case 'custom-options':
				$("#ccm-permission-detail").closest('.ui-dialog-content').parent().append('<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ccm-ui"></div>');
				break;
			case 'workflow':
				$("#ccm-permission-detail").closest('.ui-dialog-content').parent().append('<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ccm-ui"><input type="submit" class="btn primary ccm-button-right" onclick="$(\'#ccm-permissions-workflow-form\').submit()" value="<?=t('Save')?>" /></div>');
				break;
		}
		return false;
	});
	*/
	
	ccm_addAccessEntity = function(peID, pdID, accessType) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();
		
		$.get('<?=$permissionKey->getPermissionKeyToolsURL("add_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
			$.get(ccm_permissionDialogURL + '?paID=<?=$pa->getPermissionAccessID()?>&message=entity_added&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}
	
	ccm_deleteAccessEntityAssignment = function(peID) {
		jQuery.fn.dialog.showLoader();
		
		$.get('<?=$permissionKey->getPermissionKeyToolsURL("remove_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&peID=' + peID, function() { 
			$.get(ccm_permissionDialogURL + '?paID=<?=$pa->getPermissionAccessID()?>&message=entity_removed&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}

	ccm_submitPermissionsDetailForm = function() {
		jQuery.fn.dialog.showLoader();
		$("#ccm-permissions-detail-form").ajaxSubmit(function(r) {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
			// now we reload the permission key to use the new permission assignment
			$('#ccm-permission-grid-cell-<?=$permissionKey->getPermissionKeyID()?>').load(
				'<?=$permissionKey->getPermissionKeyToolsURL("display_access_cell")?>&paID=<?=$pa->getPermissionAccessID()?>', 				function() {
					$('#ccm-permission-grid-name-<?=$permissionKey->getPermissionKeyID()?> a').attr('data-paID', '<?=$pa->getPermissionAccessID()?>');		
				}
			);
		});
		return false;
	}
	
	/*
	ccm_submitPermissionWorkflowForm = function() {
		jQuery.fn.dialog.showLoader();
		$("#ccm-permissions-workflow-form").ajaxSubmit(function(r) {
			$.get(ccm_permissionDialogURL + '?message=workflows_saved&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
		return false;
	}
	*/
	
	
	
	<? if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'custom_options_saved') { ?>
		$('a[data-tab=custom-options]').click();
	<? } ?>

	<? if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'workflows_saved') { ?>
		$('a[data-tab=workflow]').click();
	<? } ?>


});
</script>
