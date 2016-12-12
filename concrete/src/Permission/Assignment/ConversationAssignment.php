<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Conversation\Conversation;
use Database;

class ConversationAssignment extends Assignment
{
    protected $permissionObjectToCheck = null;

    public function setPermissionObject($object)
    {
        $this->permissionObject = $object;

        if ($object instanceof Message) {
            $object = $object->getConversationObject();
        }

        if ($object instanceof Conversation && $object->overrideGlobalPermissions()) {
            $this->permissionObjectToCheck = $object;
        }
    }

    public function getPermissionAccessObject()
    {
        $cnvID = 0;
        if (is_object($this->permissionObjectToCheck)) {
            $cnvID = $this->permissionObjectToCheck->getConversationID();
        }

        $db = Database::connection();
        $r = $db->GetOne(
            'select paID from ConversationPermissionAssignments where cnvID = ? and pkID = ?',
            array(
                $cnvID,
                $this->pk->getPermissionKeyID(),
            )
        );

        return Access::getByID($r, $this->pk);
    }

    public function clearPermissionAssignment()
    {
        $cnvID = 0;
        if (is_object($this->permissionObject)) {
            $cnvID = $this->permissionObject->getConversationID();
        }

        $db = Database::connection();
        $db->Execute(
            'update ConversationPermissionAssignments set paID = 0 where pkID = ? and cnvID = ?',
            array($this->pk->getPermissionKeyID(), $cnvID)
        );
    }

    public function assignPermissionAccess(Access $pa)
    {
        $cnvID = 0;
        if (is_object($this->permissionObject)) {
            $cnvID = $this->permissionObject->getConversationID();
        }

        $db = Database::connection();
        $db->Replace(
            'ConversationPermissionAssignments',
            array(
                'cnvID' => $cnvID,
                'paID' => $pa->getPermissionAccessID(),
                'pkID' => $this->pk->getPermissionKeyID(),
            ),
            array(
                'cnvID',
                'pkID',
            ),
            true
        );
        $pa->markAsInUse();
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        $cnvID = 0;
        if (is_object($this->permissionObject)) {
            $cnvID = $this->permissionObject->getConversationID();
        }

        return parent::getPermissionKeyToolsURL($task) . '&cnvID=' . $cnvID;
    }
}
