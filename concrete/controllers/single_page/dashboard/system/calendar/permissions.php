<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Loader;

class Permissions extends DashboardPageController
{
    public function save()
    {
        if ($this->token->validate('save_permissions')) {
            $tp = new Checker();
            if ($tp->canAccessTaskPermissions()) {
                $permissions = Key::getList('calendar_admin');
                $valn = $this->app->make(Numbers::class);
                $pkIDs = $this->request->request->get('pkID');
                if (!is_array($pkIDs)) {
                    $pkIDs = [];
                }
                foreach ($permissions as $pk) {
                    $pt = $pk->getPermissionAssignmentObject();
                    $pt->clearPermissionAssignment();
                    $paID = array_get($pkIDs, $pk->getPermissionKeyID());
                    if ($valn->integer($paID, 1)) {
                        $pa = Access::getByID($paID, $pk);
                        if ($pa) {
                            $pt->assignPermissionAccess($pa);
                        }
                    }
                }
                $this->flash('success', t('Permissions updated successfully.'));

                return $this->buildRedirect($this->action());
            }
            $this->error->add(t('Access Denied.'));
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
    }
}
