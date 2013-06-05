<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Permissions_Tasks extends DashboardBaseController {
	
	public function save() {
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			
			$tp = new TaskPermission();
			if ($tp->canAccessTaskPermissions()) {
				$permissions = PermissionKey::getList('sitemap');
				$permissions = array_merge($permissions, PermissionKey::getList('marketplace_newsflow'));
				$permissions = array_merge($permissions, PermissionKey::getList('admin'));
				foreach($permissions as $pk) {
					$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
					$pt = $pk->getPermissionAssignmentObject();
					$pt->clearPermissionAssignment();
					if ($paID > 0) {
						$pa = PermissionAccess::getByID($paID, $pk);
						if (is_object($pa)) {
							$pt->assignPermissionAccess($pa);
						}			
					}		
				}
				$this->redirect('/dashboard/system/permissions/tasks', 'updated');
			}
			
		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
		$this->set('success', t('Permissions updated successfully.'));
	}

}