<?php
namespace Concrete\Controller\SinglePage\Dashboard\Blocks;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use PermissionKey;
use PermissionAccess;
use Concrete\Core\Legacy\TaskPermission;

class Permissions extends DashboardPageController {
	
	public function save() {
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			
			$tp = new TaskPermission();
			if ($tp->canAccessTaskPermissions()) {
				$permissions = PermissionKey::getList('block_type');
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
				$this->redirect('/dashboard/blocks/permissions', 'updated');
			}
			
		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
		$this->set('success', t('Permissions updated successfully.'));
	}

}