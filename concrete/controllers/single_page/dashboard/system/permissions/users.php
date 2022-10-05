<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Loader;
use PermissionKey;
use TaskPermission;

class Users extends DashboardPageController
{

    public function view()
    {
        // Get the pkID of permission keys we want to warn about
        $sudo = PermissionKey::getByHandle('sudo');
        $groupEdit = PermissionKey::getByHandle('edit_group_folder');

        $this->set('permissionWarnings', [
            $sudo->getPermissionKeyID() => t(
                'Users with this permission can manage aspects of all users including elevating their own permissions.'
            ),
            $groupEdit->getPermissionKeyID() => t(
                'Users who can edit groups can elevate their own permissions by moving groups.'
            ),
        ]);

        $tree = GroupTree::get();
        $root = $tree->getRootTreeNodeObject();
        $this->set('root', $root);
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

                $tree = GroupTree::get();
                $root = $tree->getRootTreeNodeObject();
                $permissions = PermissionKey::getList('group_folder');
                foreach ($permissions as $pk) {
                    $pk->setPermissionObject($root);
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
        $this->set('success', t('Permissions updated successfully.'));
        $this->view();
    }
}
