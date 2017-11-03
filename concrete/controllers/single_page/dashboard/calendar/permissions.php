<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Loader;
use Concrete\Core\Calendar\Calendar;

class Permissions extends DashboardPageController
{
    public function update_permissions_inheritance()
    {
        $calendar = Calendar::getByID($this->request->request->get('caID'));
        if (!$this->token->validate('update_permissions_inheritance')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!is_object($calendar)) {
            $this->error->add(t('Invalid calendar.'));
        }

        $cp = new \Permissions($calendar);
        if (!$cp->canEditCalendarPermissions()) {
            $this->error->add(t('Access Denied.'));
        }

        if (!$this->error->has()) {
            $override = $this->request->request->get('update_inheritance') == 'override' ? true : false;

            $manager = new Calendar\PermissionsManager($this->entityManager);

            if ($override) {
                $manager->setPermissionsToOverride($calendar);
            } else {
                $manager->clearCustomPermissions($calendar);
            }

            $this->redirect('/dashboard/calendar/permissions', 'view', $calendar->getID(), 'permission_updated');
        } else {
            $this->view($this->request->request->get('caID'));
        }
    }

    public function view($caID = null, $message = null)
    {
        if ($caID) {
            $calendar = Calendar::getByID(intval($caID));
            $cp = new \Permissions($calendar);
            if (!$cp->canEditCalendarPermissions()) {
                unset($calendar);
            }
        }

        if (!$calendar) {
            throw new \Exception(t('Access Denied.'));
        }

        $this->set('calendar', $calendar);
        if ($message == 'permissions_updated') {
            $this->set('success', t('Permissions updated successfully.'));
        }
    }

    public function save_permissions()
    {
        if (!Loader::helper('validation/token')->validate('save_permissions')) {
            $this->error->add(Loader::helper("validation/token")->getErrorMessage());
        }

        $calendar = Calendar::getByID($this->request->request->get('caID'));
        if (!is_object($calendar)) {
            $this->error->add(t('Invalid calendar.'));
        }

        $cp = new \Permissions($calendar);
        if (!$cp->canEditCalendarPermissions()) {
            $this->error->add(t('Access Denied.'));
        }

        if (!$this->error->has()) {
            $permissions = Key::getList('calendar');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($calendar);
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
            $this->redirect('/dashboard/calendar/permissions', 'view', $calendar->getID(), 'permissions_updated');
        }

        $this->view($this->request->request->get('caID'));
    }
}
