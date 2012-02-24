<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if ($cp->canAdminPage()) {

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($c);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($c);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pk->removeAssignment($pe);
	}

	if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($c);
		$pk->savePermissionKey($_POST);
	}

	if ($_REQUEST['task'] == 'change_permission_inheritance' && Loader::helper("validation/token")->validate('change_permission_inheritance')) {
		switch($_REQUEST['mode']) {
			case 'PARENT':
				$c->inheritPermissionsFromParent();
				break;
			case 'TEMPLATE':
				$c->inheritPermissionsFromDefaults();
				break;
			default:
				$c->setPermissionsToManualOverride();
				break;
		}			
	}

	if ($_REQUEST['task'] == 'change_subpage_defaults_inheritance' && Loader::helper("validation/token")->validate('change_subpage_defaults_inheritance')) {
		$c->setOverrideTemplatePermissions($_REQUEST['inherit']);
	}
	
	

}
