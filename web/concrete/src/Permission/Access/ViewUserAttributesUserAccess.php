<?php

namespace Concrete\Core\Permission\Access;

use Database;
use Concrete\Core\Permission\Key\Key as PermissionKey;

class ViewUserAttributesUserAccess extends UserAccess
{
    public function save($args = array())
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
        $db->executeQuery('delete from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        if (is_array($args['viewAttributesIncluded'])) {
            foreach ($args['viewAttributesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['viewAttributesExcluded'])) {
            foreach ($args['viewAttributesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (is_array($args['akIDInclude'])) {
            foreach ($args['akIDInclude'] as $peID => $akIDs) {
                foreach ($akIDs as $akID) {
                    $v = array($this->getPermissionAccessID(), $peID, $akID);
                    $db->executeQuery('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['akIDExclude'])) {
            foreach ($args['akIDExclude'] as $peID => $akIDs) {
                foreach ($akIDs as $akID) {
                    $v = array($this->getPermissionAccessID(), $peID, $akID);
                    $db->executeQuery('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
                }
            }
        }
    }

    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery('insert into UserPermissionViewAttributeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['akID']);
            $db->executeQuery('insert into UserPermissionViewAttributeAccessListCustom  (peID, paID, akID) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array())
    {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            if (isset($this->permissionObjectToCheck) && ($this->permissionObjectToCheck instanceof Page) && ($l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE)) {
                $permission = 'A';
            } else {
                $permission = $db->fetchColumn('select permission from UserPermissionViewAttributeAccessList where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
                if ($permission != 'N' && $permission != 'C') {
                    $permission = 'A';
                }
            }
            $l->setAttributesAllowedPermission($permission);
            if ($permission == 'C') {
                $akIDs = $db->GetCol('select akID from UserPermissionViewAttributeAccessListCustom where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
                $l->setAttributesAllowedArray($akIDs);
            }
        }

        return $list;
    }
}
