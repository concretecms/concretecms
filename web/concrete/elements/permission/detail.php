<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? 
if ($_REQUEST['paID'] && $_REQUEST['paID'] > 0) { 
	$pa = PermissionAccess::getByID($_REQUEST['paID'], $permissionKey);
	if ($pa->isPermissionAccessInUse()) {
		$pa = $pa->duplicate();
	}
} else { 
	$pa = PermissionAccess::create($permissionKey);
}

?>

<div class="ccm-ui" id="ccm-permission-detail">
<form id="ccm-permissions-detail-form" onsubmit="return ccm_submitPermissionsDetailForm()" method="post" action="<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL()?>">

<input type="hidden" name="paID" value="<?=$pa->getPermissionAccessID()?>" />

<? $workflows = Workflow::getList();?>

<? Loader::element('permission/message_list'); ?>

<?
$tabs = array();

 if ($permissionKey->hasCustomOptionsForm() || ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0)) { ?>
	<?
	$tabs[] = array('access-types', t('Access'), true);
	if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) {
		$tabs[] = array('workflow', t('Workflow'));
	}
	if ($permissionKey->hasCustomOptionsForm()) {
		$tabs[] = array('custom-options', t('Details'));
	}
	?>
	<?=Loader::helper('concrete/interface')->tabs($tabs);?>
<? } ?>
	
<? if ($permissionKey->getPermissionKeyDescription()) { ?>
<div class="dialog-help">
<?=tc('PermissionKeyDescription', $permissionKey->getPermissionKeyDescription())?>
</div>
<? } ?>


<div id="ccm-tab-content-access-types" <? if (count($tabs) > 0) { ?>class="ccm-tab-content"<? } ?>>
<?
$pkCategoryHandle = $permissionKey->getPermissionKeyCategoryHandle();
$accessTypes = $permissionKey->getSupportedAccessTypes();
Loader::element('permission/access/list', array('pkCategoryHandle' => $pkCategoryHandle, 'permissionAccess' => $pa, 'accessTypes' => $accessTypes)); ?>
</div>

<? if ($permissionKey->hasCustomOptionsForm()) { ?>
<div id="ccm-tab-content-custom-options" class="ccm-tab-content">

<? if ($permissionKey->getPackageID() > 0) { ?>
	<? Loader::packageElement('permission/keys/' . $permissionKey->getPermissionKeyHandle(), $permissionKey->getPackageHandle(), array('permissionAccess' => $pa)); ?>
<? } else { ?>
	<? Loader::element('permission/keys/' . $permissionKey->getPermissionKeyHandle(), array('permissionAccess' => $pa)); ?>
<? } ?>

</div>

<? } ?>

<? if ($permissionKey->canPermissionKeyTriggerWorkflow() && count($workflows) > 0) { ?>
	<?
	$selectedWorkflows = $pa->getWorkflows();
	$workflowIDs = array();
	foreach($selectedWorkflows as $swf) {
		$workflowIDs[] = $swf->getWorkflowID();
	}
	?>
		
	<div id="ccm-tab-content-workflow" class="ccm-tab-content">
			<h3><?=t('Attach a workflow to this permission?')?></h3>
			<div class="clearfix">
			<label><?=t('Workflow')?></label>
			<div class="input">
			<ul class="inputs-list">
				<? foreach($workflows as $wf) { ?>
					<li><label><input type="checkbox" name="wfID[]" value="<?=$wf->getWorkflowID()?>" <? if (count($wf->getRestrictedToPermissionKeyHandles()) > 0 && (!in_array($permissionKey->getPermissionKeyHandle(), $wf->getRestrictedToPermissionKeyHandles()))) { ?> disabled="disabled" <? } ?>
					<? if (in_array($wf->getWorkflowID(), $workflowIDs)) { ?> checked="checked" <? } ?> /> <span><?=$wf->getWorkflowName()?></span></label></li>
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
	
	ccm_addAccessEntity = function(peID, pdID, accessType) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();
	
		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}
	
		$.get('<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("add_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_added&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) { 
				jQuery.fn.dialog.replaceTop(r);
				jQuery.fn.dialog.hideLoader();
			});
		});
	}
	
	ccm_deleteAccessEntityAssignment = function(peID) {
		jQuery.fn.dialog.showLoader();

		if (ccm_permissionDialogURL.indexOf('?') > 0) {
			var qs = '&';
		} else {
			var qs = '?';
		}
		
		$.get('<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("remove_access_entity")?>&paID=<?=$pa->getPermissionAccessID()?>&peID=' + peID, function() { 
			$.get(ccm_permissionDialogURL + qs + 'paID=<?=$pa->getPermissionAccessID()?>&message=entity_removed&pkID=<?=$permissionKey->getPermissionKeyID()?>', function(r) { 
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
				'<?=$permissionKey->getPermissionAssignmentObject()->getPermissionKeyToolsURL("display_access_cell")?>&paID=<?=$pa->getPermissionAccessID()?>', function() {
					$('#ccm-permission-grid-name-<?=$permissionKey->getPermissionKeyID()?> a').attr('data-paID', '<?=$pa->getPermissionAccessID()?>');		
				}
			);
		});
		return false;
	}
	
	
	<? if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'custom_options_saved') { ?>
		$('a[data-tab=custom-options]').click();
	<? } ?>

	<? if (isset($_REQUEST['message']) && $_REQUEST['message'] == 'workflows_saved') { ?>
		$('a[data-tab=workflow]').click();
	<? } ?>


});
</script>
