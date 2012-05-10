<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = $b->getBlockCollectionObject();
$arHandle = $b->getAreaHandle();
?>

<? $pk = BlockPermissionKey::getByID($_REQUEST['pkID']); ?>
<? $pk->setPermissionObject($b); ?>

<? Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
ccm_addAccessEntity = function(peID, pdID, accessType) {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("add_access_entity")?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=set_advanced_permissions&message=entity_added&pkID=<?=$pk->getPermissionKeyID()?>&arHandle=<?=$arHandle?>&cID=<?=$c->getCollectionID()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=set_advanced_permissions&message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?>&arHandle=<?=$arHandle?>&cID=<?=$c->getCollectionID()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_submitPermissionCustomOptionsForm = function(peID) {
	jQuery.fn.dialog.showLoader();
	$("#ccm-permissions-custom-options-form").ajaxSubmit(function(r) {
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_block_popup?btask=set_advanced_permissions&message=custom_options_saved&pkID=<?=$pk->getPermissionKeyID()?>&arHandle=<?=$arHandle?>&cID=<?=$c->getCollectionID()?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
	return false;
}


</script>