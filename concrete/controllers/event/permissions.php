<?php
namespace Concrete\Controller\Event;

use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Calendar\Calendar;

class Permissions extends \Concrete\Core\Controller\Controller
{
    public function process()
    {
        $p = new \Concrete\Core\Permission\Checker();
        if ($p->canAccessTaskPermissions()) {
            if ($_REQUEST['task'] == 'add_access_entity' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('add_access_entity')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pd = PermissionDuration::getByID($_REQUEST['pdID']);
                $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
            }

            if ($_REQUEST['task'] == 'remove_access_entity' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('remove_access_entity')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pa->removeListItem($pe);
            }

            if ($_REQUEST['task'] == 'save_permission' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('save_permission')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pa->save($_POST);
                $pa->clearWorkflows();
                if (is_array($_POST['wfID'])) {
                    foreach ($_POST['wfID'] as $wfID) {
                        $wf = Workflow::getByID($wfID);
                        if (is_object($wf)) {
                            $pa->attachWorkflow($wf);
                        }
                    }
                }
            }

            if ($_REQUEST['task'] == 'display_access_cell' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('display_access_cell')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                \Concrete\Core\Legacy\Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
            }
        }
    }

    public function processCalendar()
    {
        $calendar = Calendar::getByID($this->request->request->get('caID'));
        $cp = new \Concrete\Core\Permission\Checker($calendar);
        if ($cp->canEditCalendarPermissions()) {
            if ($_REQUEST['task'] == 'add_access_entity' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('add_access_entity')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pk->setPermissionObject($calendar);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pd = PermissionDuration::getByID($_REQUEST['pdID']);
                $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
            }

            if ($_REQUEST['task'] == 'remove_access_entity' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('remove_access_entity')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pk->setPermissionObject($calendar);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
                $pa->removeListItem($pe);
            }

            if ($_REQUEST['task'] == 'save_permission' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('save_permission')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pk->setPermissionObject($calendar);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                $pa->save($_POST);
                $pa->clearWorkflows();
                if (is_array($_POST['wfID'])) {
                    foreach ($_POST['wfID'] as $wfID) {
                        $wf = Workflow::getByID($wfID);
                        if (is_object($wf)) {
                            $pa->attachWorkflow($wf);
                        }
                    }
                }
            }

            if ($_REQUEST['task'] == 'display_access_cell' && \Concrete\Core\Legacy\Loader::helper("validation/token")->validate('display_access_cell')) {
                $pk = \Concrete\Core\Permission\Key\Key::getByID($_REQUEST['pkID']);
                $pk->setPermissionObject($calendar);
                $pa = \Concrete\Core\Permission\Access\Access::getByID($_REQUEST['paID'], $pk);
                \Concrete\Core\Legacy\Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
            }
        }
    }
}
