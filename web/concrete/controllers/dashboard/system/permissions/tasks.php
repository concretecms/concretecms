<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsTasksController extends DashboardBaseController {
	
	public function save() {
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			
			$tp = new TaskPermission();
			if ($tp->canAccessTaskPermissions()) {
				$permissions = PermissionKey::getList('sitemap');
				$permissions = array_merge($permissions, PermissionKey::getList('marketplace_newsflow'));
				$permissions = array_merge($permissions, PermissionKey::getList('admin'));
				foreach($permissions as $pk) {
					$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
					$pk->clearPermissionAssignment();
					if ($paID > 0) {
						$pa = PermissionAccess::getByID($paID);
						if (is_object($pa)) {
							$pk->assignPermissionAccess($pa);
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