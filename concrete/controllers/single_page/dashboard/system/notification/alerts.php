<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Notification;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use PermissionKey;

class Alerts extends DashboardPageController
{
    public function view()
    {
        $key = Key::getByHandle('notify_in_notification_center');
        $permissionAccess = $key->getPermissionAssignmentObject()->getPermissionAccessObject();
        if ($permissionAccess === null) {
            $permissionAccess = PermissionAccess::create($key);
        }
        $this->set('key', $key);
        $this->set('permissionAccess', $permissionAccess);
    }

    public function save()
    {
        if (!$this->token->validate('save_permissions')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $tp = new Checker();
        if (!$tp->canAccessTaskPermissions()) {
            $this->error->add(t('Access Denied.'));
        }
        $permissionKeyIDs = $this->request->request->get('pkID', []);
        if (!is_array($permissionKeyIDs)) {
            $this->error->add(t('Invalid parameters.'));
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $permissions = PermissionKey::getList('sitemap');
        $permissions = array_merge($permissions, PermissionKey::getList('notification'));
        $permissions = array_merge($permissions, PermissionKey::getList('marketplace'));
        $permissions = array_merge($permissions, PermissionKey::getList('admin'));
        foreach ($permissions as $pk) {
            $paID = (int) ($permissionKeyIDs[$pk->getPermissionKeyID()] ?? 0);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->clearPermissionAssignment();
            if ($paID !== 0) {
                $pa = PermissionAccess::getByID($paID, $pk);
                if ($pa !== null) {
                    $pt->assignPermissionAccess($pa);
                }
            }
        }

        $this->flash('success', t('Permissions updated successfully.'));

        return $this->buildRedirect($this->action());
    }
}
