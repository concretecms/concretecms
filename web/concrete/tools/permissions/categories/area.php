<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if ($cp->canAdminPage()) {
	$a = Area::get($c, $_GET['arHandle']);
	$ax = $a; 
	$cx = $c;
	if ($a->isGlobalArea()) {
		$cx = Stack::getByName($_REQUEST['arHandle']);
		$ax = Area::get($cx, STACKS_AREA_NAME);
	}
	if (is_object($a)) {
		if ($_POST['aRevertToPagePermissions']) {
			$ax->revertToPagePermissions();		
		} else {

			if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
				$pk = AreaPermissionKey::getByID($_REQUEST['pkID'], $ax);
				$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
				$pd = PermissionDuration::getByID($_REQUEST['pdID']);
				$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
			}
		
			if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
				$pk = AreaPermissionKey::getByID($_REQUEST['pkID'], $ax);
				$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
				$pk->removeAssignment($pe);
			}
		
			if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
				$pk = AreaPermissionKey::getByID($_REQUEST['pkID'], $ax);
				$pk->savePermissionKey($_POST);
			}
		}
	}
}
