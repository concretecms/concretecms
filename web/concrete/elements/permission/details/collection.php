<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
if ($cp->canEditPagePermissions()) {
	// if it's composer mode and we have a target, then we hack the permissions collection id
	if (isset($isComposer) && $isComposer) {
		$cd = ComposerPage::getByID($c->getCollectionID());
		if ($cd->isComposerDraft()) {
			if ($cd->getComposerDraftPublishParentID() > 0) {
				if ($cd->getCollectionInheritance() == 'PARENT') {
					$c->cParentID = $cd->getComposerDraftPublishParentID();
					$cpID = $c->getParentPermissionsCollectionID();
					$c->cInheritPermissionsFromCID = $cpID;
				}
			}
		}
	}

}
?>
<div class="ccm-ui" id="ccm-page-permissions-list">

<? $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($c);
?>

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
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=set_advanced_permissions&message=entity_added&pkID=<?=$pk->getPermissionKeyID()?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	
	$.get('<?=$pk->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=set_advanced_permissions&message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?>&cID=<?=$c->getCollectionID()?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});
	});
}


</script>