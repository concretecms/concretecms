<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Loader;
use PermissionKey;
use TaskPermission;

class Users extends DashboardPageController
{

    public function view()
    {
        // Get the pkID of permission keys we want to warn about
        $sudo = PermissionKey::getByHandle('sudo');

        $this->set('permissionWarnings', [
            $sudo->getPermissionKeyID() => t(
                'Users with this permission can manage aspects of all users including elevating their own permissions.'
            )
        ]);
    }

    public function save()
    {
        if (Loader::helper('validation/token')->validate('save_permissions')) {
            $tp = new TaskPermission();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = PermissionKey::getList('user');
                foreach ($permissions as $pk) {
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
                $this->redirect('/dashboard/system/permissions/users', 'updated');
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
    }

    public function updated()
    {
        $this->view();
        $this->set('success', t('Permissions updated successfully.'));
    }
}
