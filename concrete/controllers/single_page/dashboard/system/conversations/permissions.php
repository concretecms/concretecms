<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;

use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use PermissionKey;
use TaskPermission;
use Concrete\Core\Permission\Access\Access;

class Permissions extends DashboardPageController
{
    public function save()
    {
        if (Loader::helper('validation/token')->validate('save_permissions')) {
            $tp = new TaskPermission();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = PermissionKey::getList('conversation');
                foreach ($permissions as $pk) {
                    $pk->setPermissionObject(false);
                    $paID = $_POST['pkID'][$pk->getPermissionKeyID()];
                    $pt = $pk->getPermissionAssignmentObject();
                    $pt->clearPermissionAssignment();
                    if ($paID > 0) {
                        $pa = Access::getByID($paID, $pk);
                        if (is_object($pa)) {
                            $pt->assignPermissionAccess($pa);
                        }
                    }
                }
                $this->redirect('/dashboard/system/conversations/permissions', 'updated');
            }
        } else {
            $this->error->add(Loader::helper("validation/token")->getErrorMessage());
        }
    }

    public function updated()
    {
        $this->set('success', t('Permissions updated successfully.'));
    }
}
