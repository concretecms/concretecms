<?
defined('C5_EXECUTE') or die("Access Denied.");
$p = new Permissions();
if ($p->canAccessTaskPermissions()) { 

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = MarketplaceNewsflowPermissionKey::getByID($_REQUEST['pkID']);
		$pa = MarketplaceNewsflowPermissionAccess::getByID($_REQUEST['paID'], $pk);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pa->addListITem($pe, $pd, $_REQUEST['accessType']);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = MarketplaceNewsflowPermissionKey::getByID($_REQUEST['pkID']);
		$pa = MarketplaceNewsflowPermissionAccess::getByID($_REQUEST['paID'], $pk);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pa->removeListItem($pe);
	}

	if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
		$pk = MarketplaceNewsflowPermissionKey::getByID($_REQUEST['pkID']);
		$pa = MarketplaceNewsflowPermissionAccess::getByID($_REQUEST['paID'], $pk);
		$pa->save($_POST);
	}

	if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
		$pk = MarketplaceNewsflowPermissionKey::getByID($_REQUEST['pkID']);
		$pa = MarketplaceNewsflowPermissionAccess::getByID($_REQUEST['paID'], $pk);
		Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
	}
	
}

