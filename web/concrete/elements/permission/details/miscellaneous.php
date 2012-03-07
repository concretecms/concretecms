<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui" id="ccm-miscellaneous-set-permissions-list">

<? $pk = PermissionKey::getByID($_REQUEST['pkID']);?>

<? if ($pk->getPermissionKeyDescription()) { ?>
<div class="dialog-help">
<?=$pk->getPermissionKeyDescription()?>
</div>
<? } ?>

<? Loader::element('permission/message_list'); ?>

<?
$accessTypes = $pk->getSupportedAccessTypes();
Loader::element('permission/access_list', array('permissionKey' => $pk, 'accessTypes' => $accessTypes)); ?>

<? if ($pk->getPackageID() > 0) { ?>
	<? Loader::packageElement('permission/keys/' . $pk->getPermissionKeyHandle(), $pk->getPackageHandle(), array('permissionKey' => $pk)); ?>
<? } else { ?>
	<? Loader::element('permission/keys/' . $pk->getPermissionKeyHandle(), array('permissionKey' => $pk)); ?>
<? } ?>
</div>

<script type="text/javascript">
ccm_addAccessEntity = function(peID, pdID, accessType) {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("add_access_entity")?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/user?message=entity_added&pkID=<?=$pk->getPermissionKeyID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/user?message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}


</script>