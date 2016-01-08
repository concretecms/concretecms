<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Config;
use TaskPermission;
use PageType;
use PermissionAccess;
use PermissionKey;
class Permissions extends DashboardPageController {

	public function view($ptID = false, $message = false) {
		$this->pagetype = PageType::getByID($ptID);
		if (!$this->pagetype) {
			$this->redirect('/dashboard/pages/types');
		}
        $cmp = new \Permissions($this->pagetype);
        if (!$cmp->canEditPageTypePermissions()) {
            throw new \Exception(t('You do not have access to edit this page type permissions.'));
        }
		switch($message) {
			case 'updated':
				$this->set('success', t('Permissions updated successfully.'));
				break;
		}
        $this->set('defaultPage', $this->pagetype->getPageTypePageTemplateDefaultPageObject());
		$this->set('pagetype', $this->pagetype);
	}

	public function save() {
		$this->view($this->post('ptID'));
		if (Loader::helper('validation/token')->validate('save_permissions')) {
            $permissions = PermissionKey::getList('page_type');
            foreach($permissions as $pk) {
                $pk->setPermissionObject($this->pagetype);
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

            if (Config::get('concrete.permissions.model') == 'advanced') {
                $permissions = PermissionKey::getList('page');
                $defaultPage = $this->pagetype->getPageTypePageTemplateDefaultPageObject();
                foreach($permissions as $pk) {
                    $pk->setPermissionObject($defaultPage);
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
            }
            $this->redirect('/dashboard/pages/types/permissions', $this->pagetype->getPageTypeID(), 'updated');

		} else {
			$this->error->add(Loader::helper("validation/token")->getErrorMessage());
		}
		
	}
	
	public function updated() {
	}

}