<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Key\Key;
use Loader;
use Concrete\Core\Permission\Key\Key as PermissionKey;

class AddConversationMessageConversationAccess extends ConversationAccess
{

    public function save($args)
    {
        parent::save();
        $db = Loader::db();
        $db->Execute('delete from ConversationPermissionAddMessageAccessList where paID = ?',
            array($this->getPermissionAccessID()));
        if (is_array($args['addMessageApproval'])) {
            foreach ($args['addMessageApproval'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->Execute('insert into ConversationPermissionAddMessageAccessList (paID, peID, permission) values (?, ?, ?)',
                    $v);
            }
        }
    }

    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Loader::db();
        $r = $db->Execute('select * from ConversationPermissionAddMessageAccessList where paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->Execute('insert into ConversationPermissionAddMessageAccessList (peID, paID, permission) values (?, ?, ?)',
                $v);
        }
        return $newPA;
    }


    public function getAccessListItems(
        $accessType = Key::ACCESS_TYPE_INCLUDE,
        $filterEntities = array()
    ) {
        $db = Loader::db();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            $permission = $db->GetOne(
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
