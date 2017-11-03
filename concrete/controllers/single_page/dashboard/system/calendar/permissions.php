<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use PermissionKey;
use TaskPermission;
use PermissionAccess;

class Permissions extends DashboardPageController
{
    public function save()
    {
        if (Loader::helper('validation/token')->validate('save_permissions')) {
            $tp = new TaskPermission();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = PermissionKey::getList('calendar_admin');
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
                $this->redirect('/dashboard/system/calendar/permissions', 'updated');
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
