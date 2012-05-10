<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($_REQUEST['fsID'] > 0) {
	$fs = FileSet::getByID($_REQUEST['fsID']);
} else {
	$fs = FileSet::getGlobal();
}
$fsp = new Permissions($fs);
if ($fsp->canEditFileSetPermissions()) {

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = FileSetPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($fs);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = FileSetPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($fs);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pk->removeAssignment($pe);
	}

	if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
		$pk = FileSetPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($fs);
		$pk->savePermissionKey($_POST);
	}

	if ($_REQUEST['task'] == 'save_workflows' && Loader::helper("validation/token")->validate('save_workflows')) {
		$pk = FileSetPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($fs);
		$pk->clearWorkflows();
		foreach($_POST['wfID'] as $wfID) {
			$wf = Workflow::getByID($wfID);
			if (is_object($wf)) {
				$pk->attachWorkflow($wf);
			}
		}
	}

}
