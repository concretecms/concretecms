<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Legacy\Loader;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\File\Set\Set as FileSet;

class Permissions extends DashboardPageController
{
    public function save()
    {
        if (Loader::helper('validation/token')->validate('save_permissions')) {
            $root = (new Filesystem())->getRootFolder();
            $tp = new TaskPermission();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = PermissionKey::getList('file_folder');
                foreach ($permissions as $pk) {
                    $pk->setPermissionObject($root);
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
                $this->redirect('/dashboard/system/files/permissions', 'updated');
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
