<?
defined('C5_EXECUTE') or die("Access Denied.");
$p = Page::getByPath('/dashboard/workflow/list');
$cp = new Permissions($p);
if ($cp->canViewPage()) { 

	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$wf = Workflow::getByID($_REQUEST['wfID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		$wf->addAssignment($pe, $pd);
	}

	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = FilePermissionKey::getByID($_REQUEST['pkID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pk->removeAssignment($pe);
	}

}

