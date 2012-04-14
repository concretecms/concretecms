<?
defined('C5_EXECUTE') or die("Access Denied.");
$f = File::getByID($_REQUEST['fID']);
if (is_object($f)) {
	$fp = new Permissions($f);
	if ($fp->canEditFilePermissions()) { 
	
		if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
			$pk = FilePermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($f);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pd = PermissionDuration::getByID($_REQUEST['pdID']);
			$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
		}

		if ($_REQUEST['task'] == 'revert_to_global_file_permissions' && Loader::helper("validation/token")->validate('revert_to_global_file_permissions')) {
			$f->resetPermissions();		
		}

		if ($_REQUEST['task'] == 'override_global_file_permissions' && Loader::helper("validation/token")->validate('override_global_file_permissions')) {
			$f->resetPermissions(1);		
		}
	
		if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
			$pk = FilePermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($f);
			$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
			$pk->removeAssignment($pe);
		}
	
		if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
			$pk = FilePermissionKey::getByID($_REQUEST['pkID']);
			$pk->setPermissionObject($f);
			$pk->savePermissionKey($_POST);
		}
	
	}
}

