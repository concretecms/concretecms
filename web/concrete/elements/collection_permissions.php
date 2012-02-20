<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
if ($cp->canAdminPage()) {
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
	
	if ($_REQUEST['subtask'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pk->removeAssignment($c, $pe);
	}

	if ($_REQUEST['subtask'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pk->addAssignment($c, $pe, $pd, $_REQUEST['accessType']);
	}

}
?>
<div class="ccm-ui" id="ccm-page-permissions-list">
<? $pk = PagePermissionKey::getByID($_REQUEST['pkID']); ?>

<? $included = $pk->getAssignmentList($c); ?>
<? $excluded = $pk->getAssignmentList($c, PermissionKey::ACCESS_TYPE_EXCLUDE); ?>

<h3><?=t('Included')?></h3>
<? Loader::element('permission/access_list', array('permissionKey' => $pk, 'list' => $included)); ?>

<h3><?=t('Excluded')?></h3>
<? Loader::element('permission/access_list', array('permissionKey' => $pk, 'list' => $excluded, 'accessType' => PermissionKey::ACCESS_TYPE_EXCLUDE)); ?>

</div>

<script type="text/javascript">
ccm_addAccessEntity = function(peID, pdID, accessType) {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	$('#ccm-page-permissions-list').closest('.ui-dialog-content').load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?<?=Loader::helper("validation/token")->getParameter("add_access_entity")?>&cID=<?=$_REQUEST["cID"]?>&pkID=<?=$_REQUEST["pkID"]?>&ctask=set_advanced_permissions&subtask=add_access_entity&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function() {
		jQuery.fn.dialog.hideLoader();
		$('.dialog-launch').dialog();
	});
}

ccm_deleteAccessEntityAssignment = function(peID) {
	jQuery.fn.dialog.showLoader();
	$('#ccm-page-permissions-list').closest('.ui-dialog-content').load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?<?=Loader::helper("validation/token")->getParameter("remove_access_entity")?>&cID=<?=$_REQUEST["cID"]?>&pkID=<?=$_REQUEST["pkID"]?>&ctask=set_advanced_permissions&subtask=remove_access_entity&peID=' + peID, function() {
		jQuery.fn.dialog.hideLoader();
		$('.dialog-launch').dialog();
	});
}


</script>