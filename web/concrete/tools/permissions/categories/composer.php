<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($_REQUEST['cmpID'] > 0) {
	$cmp = Composer::getByID($_REQUEST['cmpID']);
}
$ch = Page::getByPath('/dashboard/composer/list');
$chp = new Permissions($ch);
if ($chp->canViewPage()) {

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = PermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($cmp);
		$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pa->addListItem($pe, $pd, $_REQUEST['accessType']);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = PermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($cmp);
		$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pa->removeListItem($pe);
	}

	if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
		$pk = PermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($cmp);

		$pt = $pk->getPermissionAssignmentObject();
		$pt->clearPermissionAssignment();

		if ($_REQUEST['paID'] > 0) {
			$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
			if (is_object($pa)) {
				$pt->assignPermissionAccess($pa);
			}			
		}		
	}

	if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
		$pk = PermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($cmp);
		$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
		Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
	}

}
