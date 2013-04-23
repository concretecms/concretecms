<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Permissions extends DashboardBaseController {

	public function view($cmpID = false, $message = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		switch($message) {
			case 'updated':
				$this->set('success', t('Permissions updated successfully.'));
				break;
		}
		$this->set('composer', $this->composer);
	}

	public function save() {
		$this->view($this->post('cmpID'));
		if (Loader::helper('validation/token')->validate('save_permissions')) {
			$tp = new TaskPermission();
			if ($tp->canAccessComposerPermissions()) {
				$permissions = PermissionKey::getList('composer');
				foreach($permissions as $pk) {
					$pk->setPermissionObject($this->composer);
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
				$this->redirect('/dashboard/composer/list/permissions', $this->composer->getComposerID(), 'updated');
			}
			
		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
	}

}