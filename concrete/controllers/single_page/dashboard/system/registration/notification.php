<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Key\Key;
use Loader;
use PermissionKey;
use TaskPermission;
use Concrete\Core\Permission\Access\Access as PermissionAccess;

class Notification extends DashboardPageController
{
    public function save()
    {
        if (Loader::helper('validation/token')->validate('save_permissions')) {
            $tp = new TaskPermission();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = PermissionKey::getList('sitemap');
                $permissions = array_merge($permissions, PermissionKey::getList('notification'));
                $permissions = array_merge($permissions, PermissionKey::getList('marketplace_newsflow'));
                $permissions = array_merge($permissions, PermissionKey::getList('admin'));
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
                $this->redirect('/dashboard/system/registration/notification', 'updated');
            }
        } else {
            $this->error->add(Loader::helper("validation/token")->getErrorMessage());
        }
        $this->view();
    }

    public function updated()
    {
        $this->set('success', t('Permissions updated successfully.'));
        $this->view();
    }

    public function view()
    {
        $key = Key::getByHandle('notify_in_notification_center');
        $this->set('key', $key);
    }
}
