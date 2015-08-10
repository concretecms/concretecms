<?php

namespace Concrete\Core\Permission\Access;

use Concrete\Core\Page\Page;
use Concrete\Core\Area\Area;
use Database;

class AddBlockToAreaAreaAccess extends AreaAccess
{
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery('insert into AreaPermissionBlockTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['btID']);
            $db->executeQuery('insert into AreaPermissionBlockTypeAccessListCustom  (peID, paID, btID) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function getAccessListItems($accessType = AreaPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array())
    {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        $pobj = $this->getPermissionObjectToCheck();
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            if ($pobj instanceof Page) {
                $permission = $db->fetchColumn('select permission from BlockTypePermissionBlockTypeAccessList where paID = ?', array($l->getPermissionAccessID()));
            } else {
                $permission = $db->fetchColumn('select permission from AreaPermissionBlockTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
            }
            if ($permission != 'N' && $permission != 'C') {
                $permission = 'A';
            }
            $l->setBlockTypesAllowedPermission($permission);
            if ($permission == 'C') {
                if ($pobj instanceof Area) {
                    $btIDs = $db->GetCol('select btID from AreaPermissionBlockTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
                } else {
                    $btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($l->getPermissionAccessID()));
                }
                $l->setBlockTypesAllowedArray($btIDs);
            }
        }

        return $list;
    }

    public function save($args = array())
    {
        $db = Database::connection();
        parent::save();
        $db->executeQuery('delete from AreaPermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
        $db->executeQuery('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        if (is_array($args['blockTypesIncluded'])) {
            foreach ($args['blockTypesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['blockTypesExcluded'])) {
            foreach ($args['blockTypesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['btIDInclude'])) {
            foreach ($args['btIDInclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = array($this->getPermissionAccessID(), $peID, $btID);
                    $db->executeQuery('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['btIDExclude'])) {
            foreach ($args['btIDExclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = array($this->getPermissionAccessID(), $peID, $btID);
                    $db->executeQuery('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }
    }
}
