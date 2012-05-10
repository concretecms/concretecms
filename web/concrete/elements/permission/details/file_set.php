<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<? 
$pk = FileSetPermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($fileset);
?>

<? Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
ccm_addAccessEntity = function(peID, pdID, accessType) {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("add_access_entity")?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?message=entity_added&pkID=<?=$pk->getPermissionKeyID()?>&fsID=<?=$fileset->getFileSetID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?>&fsID=<?=$fileset->getFileSetID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_submitPermissionCustomOptionsForm = function() {
	jQuery.fn.dialog.showLoader();
	$("#ccm-permissions-custom-options-form").ajaxSubmit(function(r) {
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?message=custom_options_saved&pkID=<?=$pk->getPermissionKeyID()?>&fsID=<?=$fileset->getFileSetID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
	return false;
}

ccm_submitPermissionWorkflowForm = function() {
	jQuery.fn.dialog.showLoader();
	$("#ccm-permissions-workflow-form").ajaxSubmit(function(r) {
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/file_set?message=workflows_saved&pkID=<?=$pk->getPermissionKeyID()?>&fsID=<?=$fileset->getFileSetID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
	return false;
}


</script>