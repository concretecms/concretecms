<?php
namespace Concrete\Core\Permission\Access;

use Database;
use Concrete\Core\Page\Page;
use PermissionKey;

class AddBlockBlockTypeAccess extends BlockTypeAccess
{
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from BlockTypePermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['btID']);
            $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessListCustom  (peID, paID, btID) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function save($args = array())
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from BlockTypePermissionBlockTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
        $db->executeQuery('delete from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        if (is_array($args['blockTypesIncluded'])) {
            foreach ($args['blockTypesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['blockTypesExcluded'])) {
            foreach ($args['blockTypesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['btIDInclude'])) {
            foreach ($args['btIDInclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = array($this->getPermissionAccessID(), $peID, $btID);
                    $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['btIDExclude'])) {
            foreach ($args['btIDExclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = array($this->getPermissionAccessID(), $peID, $btID);
                    $db->executeQuery('insert into BlockTypePermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }
    }

    public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array(), $checkCache = true)
    {

        if ($checkCache) {
            $cache = \Core::make('cache/request');
            $item = $cache->getItem($this->getCacheIdentifier($accessType, $filterEntities));
            if (!$item->isMiss()) {
                return $item->get();
            }
            $item->lock();
        }

        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities, false);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            if (isset($this->permissionObjectToCheck) && ($this->permissionObjectToCheck instanceof Page) && ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE)) {
                $permission = 'A';
            } else {
                $permission = $db->fetchColumn('select permission from BlockTypePermissionBlockTypeAccessList where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
                if ($permission != 'N' && $permission != 'C') {
                    $permission = 'A';
                }
            }
            $l->setBlockTypesAllowedPermission($permission);
            if ($permission == 'C') {
                $btIDs = $db->GetCol('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
                $l->setBlockTypesAllowedArray($btIDs);
            }
        }

        if ($checkCache) {
            $cache->save($item->set($list));
        }


        return $list;
    }
}
