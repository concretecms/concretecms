<?
defined('C5_EXECUTE') or die("Access Denied.");
$p = Page::getByPath('/dashboard/workflow/list');
$cp = new Permissions($p);
$json = Loader::helper('json');
$workflow = Workflow::getByID($_REQUEST['wfID']);

if ($cp->canViewPage()) { 

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = BasicWorkflowPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($workflow);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$pk->addAssignment($pe, $pd, $_REQUEST['accessType']);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = BasicWorkflowPermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($workflow);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pk->removeAssignment($pe);
	}

}

