<?php

namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Key\Key;
use Database;

class AddConversationMessageConversationAccess extends ConversationAccess
{
    public function save($args = array())
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from ConversationPermissionAddMessageAccessList where paID = ?',
            array($this->getPermissionAccessID()));
        if (is_array($args['addMessageApproval'])) {
            foreach ($args['addMessageApproval'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into ConversationPermissionAddMessageAccessList (paID, peID, permission) values (?, ?, ?)',
                    $v);
            }
        }
    }

    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from ConversationPermissionAddMessageAccessList where paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery('insert into ConversationPermissionAddMessageAccessList (peID, paID, permission) values (?, ?, ?)',
                $v);
        }

        return $newPA;
    }

    public function getAccessListItems(
        $accessType = Key::ACCESS_TYPE_INCLUDE,
        $filterEntities = array()
    ) {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            $permission = $db->fetchColumn(
                'SELECT permission FROM ConversationPermissionAddMessageAccessList WHERE peID = ? AND paID = ?',
                array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
            if ($permission != 'U') {
                $permission = 'A';
            }
            $l->setNewConversationMessageApprovalStatus($permission);
        }

        return $list;
    }
}
