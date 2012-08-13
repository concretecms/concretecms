<?
defined('C5_EXECUTE') or die("Access Denied.");
$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) { 
		$c = Page::getByID($cID);
		$cp = new Permissions($c);
		if ($cp->canEditPagePermissions()) {
			$pages[] = $c;
		}
	}
} else {
	$c = Page::getByID($_REQUEST['cID']);
	$cp = new Permissions($c);
	if ($cp->canEditPagePermissions()) {
		$pages[] = $c;
	}
}

if (count($pages) > 0) { 
	if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		$pd = PermissionDuration::getByID($_REQUEST['pdID']);
		foreach($pages as $c) { 
			$pk->setPermissionObject($c);
			$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
			$pa->addListItem($pe, $pd, $_REQUEST['accessType']);
		}
	}
	
	if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
		foreach($pages as $c) { 
			$pk->setPermissionObject($c);
			$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
			$pa->removeListItem($pe);
		}
	}
	
	if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
		$pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		foreach($pages as $c) { 
			$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
			$pa->save($_POST);
			$pa->clearWorkflows();
			if (is_array($_POST['wfID'])) { 
				foreach($_POST['wfID'] as $wfID) {
					$wf = Workflow::getByID($wfID);
					if (is_object($wf)) {
						$pa->attachWorkflow($wf);
					}
				}
			}
		}
	}

	if ($_REQUEST['task'] == 'change_permission_inheritance' && Loader::helper("validation/token")->validate('change_permission_inheritance')) {
		$deferred = false;
		foreach($pages as $c) { 
			if ($c->getCollectionID() == HOME_CID) {
				continue;
			}
			
			$pkr = new ChangePagePermissionsInheritancePageWorkflowRequest();
			$pkr->setRequestedPage($c);
			$pkr->setPagePermissionsInheritance($_REQUEST['mode']);
			$pkr->setRequesterUserID($u->getUserID());
			$response = $pkr->trigger();
			if (!($response instanceof WorkflowProgressResponse)) {
				$deferred = true;
			}
		}
		$obj = new stdClass;
		$obj->deferred = $deferred;
		print Loader::helper('json')->encode($obj);
		exit;		
	}
	
	if ($_REQUEST['task'] == 'change_subpage_defaults_inheritance' && Loader::helper("validation/token")->validate('change_subpage_defaults_inheritance')) {
		$deferred = false;
		foreach($pages as $c) { 

			$pkr = new ChangeSubpageDefaultsInheritancePageWorkflowRequest();
			$pkr->setRequestedPage($c);
			$pkr->setPagePermissionsInheritance($_REQUEST['inherit']);
			$pkr->setRequesterUserID($u->getUserID());
			$response = $pkr->trigger();
			if (!($response instanceof WorkflowProgressResponse)) {
				$deferred = true;
			}
		}
		$obj = new stdClass;
		$obj->deferred = $deferred;
		print Loader::helper('json')->encode($obj);
		exit;		

	}		

	if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
		$pk = PermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($c);
		$pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
		Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
	}

	if ($_REQUEST['task'] == 'save_permission_assignments' && Loader::helper("validation/token")->validate('save_permission_assignments')) {
		$u = new User();
		$permissions = PermissionKey::getList('page');
		$deferred = false;
		foreach($pages as $c) { 
			$pkr = new ChangePagePermissionsPageWorkflowRequest();
			$pkr->setRequestedPage($c);
			$ps = new PermissionSet();
			$ps->setPermissionKeyCategory('page');
			foreach($permissions as $pk) {
				$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
				$ps->addPermissionAssignment($pk->getPermissionKeyID(), $paID);
			}
			$pkr->setPagePermissionSet($ps);
			$pkr->setRequesterUserID($u->getUserID());
			$u->unloadCollectionEdit($c);
			$response = $pkr->trigger();
			if (!($response instanceof WorkflowProgressResponse)) {
				$deferred = true;
			}
		}
		
		$obj = new stdClass;
		$obj->deferred = $deferred;
		print Loader::helper('json')->encode($obj);
		exit;		

	}

}



