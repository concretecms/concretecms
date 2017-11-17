<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Entity\Calendar\Calendar;

class CalendarAssignment extends Assignment
{
    protected $calendar;
    /**
     * @var Calendar
     */
    protected $permissionObject;

    protected $permissionObjectToCheck;
    protected $inheritedPermissions = array(
        'view_calendar' => 'view_calendars',
        'view_calendar_in_edit_interface' => 'edit_calendars',
        'edit_calendar_permissions' => 'edit_calendars_permissions',
        'edit_calendar_event_more_details_location' => 'edit_calendars_permissions',
        'edit_calendar' => 'edit_calendars',
        'add_calendar_event' => 'add_calendar_events',
        'approve_calendar_event' => 'approve_calendar_events',
        'edit_calendar_events' => 'add_calendar_events',
        'access_calendar_rss_feed' => 'access_calendar_rss_feeds',
        'delete_calendar' => 'delete_calendars',
    );

    /**
     * @param $calendar Calendar
     */
    public function setPermissionObject($calendar)
    {
        $this->permissionObject = $calendar;

        // if the area overrides the collection permissions explicitly (with a one on the override column) we check
        if ($calendar->arePermissionsSetToOverride()) {
            $this->permissionObjectToCheck = $calendar;
        }
    }

    public function getPermissionAccessObject()
    {
        $db = \Database::connection();
        if ($this->permissionObjectToCheck instanceof Calendar) {
            $r = $db->GetOne('select paID from CalendarPermissionAssignments where caID = ? and pkID = ?', array(
                $this->permissionObject->getID(), $this->pk->getPermissionKeyID(),
            ));
            if ($r) {
                return Access::getByID($r, $this->pk, false);
            }
        } elseif (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $pk = Key::getByHandle($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]);
            $pae = $pk->getPermissionAccessObject();

            return $pae;
        }

        return false;
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        $calendar = $this->getPermissionObject();

        return parent::getPermissionKeyToolsURL($task) . '&caID=' . $calendar->getId();
    }

    public function clearPermissionAssignment()
    {
        $db = \Database::connection();
        $calendar = $this->getPermissionObject();
        $db->Execute('update CalendarPermissionAssignments set paID = 0 where pkID = ? and caID = ?', array($this->pk->getPermissionKeyID(), $calendar->getID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = \Database::connection();
        $db->Replace(
            'CalendarPermissionAssignments',
            array(
                'caID' => $this->getPermissionObject()->getID(),
                'paID' => $pa->getPermissionAccessID(),
                'pkID' => $this->pk->getPermissionKeyID(),
            ),
            array('caID', 'pkID'),
            true
        );
        $pa->markAsInUse();
    }
}
