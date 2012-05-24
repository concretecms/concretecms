<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsUsersController extends DashboardBaseController {
	
	public function save() {
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			
			$tp = new TaskPermission();
			if ($tp->canAccessTaskPermissions()) {
				$permissions = PermissionKey::getList('user');
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
				$this->redirect('/dashboard/system/permissions/users', 'updated');
			}
			
		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
		$this->set('success', t('Permissions updated successfully.'));
	}

}