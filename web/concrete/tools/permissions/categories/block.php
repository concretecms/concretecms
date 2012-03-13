<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_REQUEST['arHandle']);
if (is_object($a)) {
	$ax = $a;
	$cx = $c;
	if ($a->isGlobalArea()) {
		$ax = STACKS_AREA_NAME;
		$cx = Stack::getByName($_REQUEST['arHandle']);
	}

	$b = Block::getByID($_REQUEST['bID'], $cx, $ax); 
	$p = new Permissions($b);
	// we're updating the groups for a particular block
	if ($p->canEditBlockPermissions()) {
		$nvc = $cx->getVersionToModify();
		if ($a->isGlobalArea()) {
			$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
			$xvc->relateVersionEdits($nvc);
		}
		$b->loadNewCollection($nvc);

		if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
			$pk = BlockPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($b);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pd = PermissionDuration::getByID($_REQUEST['pdID']);
			$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
		}

		if ($_REQUEST['task'] == 'set_timed_guest_access' && Loader::helper("validation/token")->validate('set_timed_guest_access')) {
			$b->doOverrideAreaPermissions();
			$pk = PermissionKey::getByHandle('view_block');
			$pk->setPermissionObject($b);
			$pe = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));
			$pd = PermissionDuration::translateFromRequest();
			$pk->addAssignment($pe, $pd, BlockPermissionKey::ACCESS_TYPE_INCLUDE);
		}
		
		if ($_REQUEST['task'] == 'revert_to_area_permissions' && Loader::helper("validation/token")->validate('revert_to_area_permissions')) {
			$b->revertToAreaPermissions();		
		}

		if ($_REQUEST['task'] == 'override_area_permissions' && Loader::helper("validation/token")->validate('override_area_permissions')) {
			$b->doOverrideAreaPermissions();		
		}
	
		if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
			$pk = BlockPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($b);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pk->removeAssignment($pe);
		}
	
		if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
			$pk = BlockPermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($b);
			$pk->savePermissionKey($_POST);
		}
	}
}
