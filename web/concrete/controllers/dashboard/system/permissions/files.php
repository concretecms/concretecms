<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsFilesController extends DashboardBaseController {
	
	public function save() {
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			$fs = FileSet::getGlobal();
			$tp = new TaskPermission();
			if ($tp->canAccessTaskPermissions()) {
				$permissions = PermissionKey::getList('file_set');
				foreach($permissions as $pk) {
					$pk->setPermissionObject($fs);
					$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
					$pk->clearPermissionAssignment();
					if ($paID > 0) {
						$pa = PermissionAccess::getByID($paID, $pk);
						if (is_object($pa)) {
							$pk->assignPermissionAccess($pa);
						}			
					}		
				}
				$this->redirect('/dashboard/system/permissions/files', 'updated');
			}
			
		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
		$this->set('success', t('Permissions updated successfully.'));
	}

}