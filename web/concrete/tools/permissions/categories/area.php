<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_GET['arHandle']);
$ax = $a; 
$cx = $c;
if ($a->isGlobalArea()) {
	$cx = Stack::getByName($_REQUEST['arHandle']);
	$ax = Area::get($cx, STACKS_AREA_NAME);
}
if (is_object($a)) {
	$ap = new Permissions($a);
	if ($ap->canEditAreaPermissions()) { 
		if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
			$pk = AreaPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($ax);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pd = PermissionDuration::getByID($_REQUEST['pdID']);
			$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
		}
	
		if ($_REQUEST['task'] == 'revert_to_page_permissions' && Loader::helper("validation/token")->validate('revert_to_page_permissions')) {
			$ax->revertToPagePermissions();		
		}
	
		if ($_REQUEST['task'] == 'override_page_permissions' && Loader::helper("validation/token")->validate('override_page_permissions')) {
			$ax->overridePagePermissions();		
		}
	
		if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
			$pk = AreaPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($ax);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pk->removeAssignment($pe);
		}
	
		if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
			$pk = AreaPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($ax);
			$pk->savePermissionKey($_POST);
		}
	}
}
