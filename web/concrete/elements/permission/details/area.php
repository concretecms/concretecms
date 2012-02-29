<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = $a->getAreaCollectionObject();
?>
<div class="ccm-ui" id="ccm-area-permissions-list">

<? $pk = AreaPermissionKey::getByID($_REQUEST['pkID']); ?>
<? $pk->setPermissionObject($a); ?>

<? if ($pk->getPermissionKeyDescription()) { ?>
<div class="dialog-help">
<?=$pk->getPermissionKeyDescription()?>
</div>
<? } ?>

<? Loader::element('permission/message_list'); ?>

<? $included = $pk->getAssignmentList(); ?>
<? $excluded = $pk->getAssignmentList(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>

<h3><?=t('Included')?></h3>
<? Loader::element('permission/access_list', array('permissionKey' => $pk, 'list' => $included)); ?>

<h3><?=t('Excluded')?></h3>
<? Loader::element('permission/access_list', array('permissionKey' => $pk, 'list' => $excluded, 'accessType' => PermissionKey::ACCESS_TYPE_EXCLUDE)); ?>

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
	
	$.get('<?=$pk->getPermissionKeyToolsURL("add_access_entity")?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=set_advanced_permissions&message=entity_added&pkID=<?=$pk->getPermissionKeyID()?>&arHandle=<?=$a->getAreaHandle()?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?atask=set_advanced_permissions&message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?>&arHandle=<?=$a->getAreaHandle()?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}


</script>