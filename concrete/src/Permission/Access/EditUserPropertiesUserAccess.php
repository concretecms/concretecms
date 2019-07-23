<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Permission\Key\UserKey as UserPermissionKey;
use Database;

class EditUserPropertiesUserAccess extends UserAccess
{
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery('select * from UserPermissionEditPropertyAccessList where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$newPA->getPermissionAccessID(),
            $row['peID'],
            $row['attributePermission'],
            $row['uName'],
            $row['uEmail'],
            $row['uPassword'],
            $row['uAvatar'],
            $row['uTimezone'],
            $row['uDefaultLanguage'],
            ];
            $db->executeQuery('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
        }
        $r = $db->executeQuery('select * from UserPermissionEditPropertyAttributeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        while ($row = $r->FetchRow()) {
            $v = [$row['peID'], $newPA->getPermissionAccessID(), $row['akID']];
            $db->executeQuery('insert into UserPermissionEditPropertyAttributeAccessListCustom (peID, paID, akID) values (?, ?, ?)', $v);
        }

        return $newPA;
    }

    public function save($args = [])
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery('delete from UserPermissionEditPropertyAccessList where paID = ?', [$this->getPermissionAccessID()]);
        $db->executeQuery('delete from UserPermissionEditPropertyAttributeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        if (is_array($args['propertiesIncluded'])) {
            foreach ($args['propertiesIncluded'] as $peID => $attributePermission) {
                $allowEditUName = 0;
                $allowEditUEmail = 0;
                $allowEditUPassword = 0;
                $allowEditUAvatar = 0;
                $allowEditUTimezone = 0;
                $allowEditUDefaultLanguage = 0;
                if (!empty($args['allowEditUName'][$peID])) {
                    $allowEditUName = $args['allowEditUName'][$peID];
                }
                if (!empty($args['allowEditUEmail'][$peID])) {
                    $allowEditUEmail = $args['allowEditUEmail'][$peID];
                }
                if (!empty($args['allowEditUPassword'][$peID])) {
                    $allowEditUPassword = $args['allowEditUPassword'][$peID];
                }
                if (!empty($args['allowEditUAvatar'][$peID])) {
                    $allowEditUAvatar = $args['allowEditUAvatar'][$peID];
                }
                if (!empty($args['allowEditUTimezone'][$peID])) {
                    $allowEditUTimezone = $args['allowEditUTimezone'][$peID];
                }
                if (!empty($args['allowEditUDefaultLanguage'][$peID])) {
                    $allowEditUDefaultLanguage = $args['allowEditUDefaultLanguage'][$peID];
                }
                $v = [$this->getPermissionAccessID(), $peID, $attributePermission, $allowEditUName, $allowEditUEmail, $allowEditUPassword, $allowEditUAvatar, $allowEditUTimezone, $allowEditUDefaultLanguage];
                $db->executeQuery('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
            }
        }

        if (is_array($args['propertiesExcluded'])) {
            foreach ($args['propertiesExcluded'] as $peID => $attributePermission) {
                $allowEditUNameExcluded = 0;
                $allowEditUEmailExcluded = 0;
                $allowEditUPasswordExcluded = 0;
                $allowEditUAvatarExcluded = 0;
                $allowEditUTimezoneExcluded = 0;
                $allowEditUDefaultLanguageExcluded = 0;
                if (!empty($args['allowEditUNameExcluded'][$peID])) {
                    $allowEditUNameExcluded = $args['allowEditUNameExcluded'][$peID];
                }
                if (!empty($args['allowEditUEmailExcluded'][$peID])) {
                    $allowEditUEmailExcluded = $args['allowEditUEmailExcluded'][$peID];
                }
                if (!empty($args['allowEditUPasswordExcluded'][$peID])) {
                    $allowEditUPasswordExcluded = $args['allowEditUPasswordExcluded'][$peID];
                }
                if (!empty($args['allowEditUAvatarExcluded'][$peID])) {
                    $allowEditUAvatarExcluded = $args['allowEditUAvatarExcluded'][$peID];
                }
                if (!empty($args['allowEditUTimezoneExcluded'][$peID])) {
                    $allowEditUTimezoneExcluded = $args['allowEditUTimezoneExcluded'][$peID];
                }
                if (!empty($args['allowEditUDefaultLanguageExcluded'][$peID])) {
                    $allowEditUDefaultLanguageExcluded = $args['allowEditUDefaultLanguageExcluded'][$peID];
                }
                $v = [$this->getPermissionAccessID(), $peID, $attributePermission, $allowEditUNameExcluded, $allowEditUEmailExcluded, $allowEditUPasswordExcluded, $allowEditUAvatarExcluded, $allowEditUTimezoneExcluded, $allowEditUDefaultLanguageExcluded];
                $db->executeQuery('insert into UserPermissionEditPropertyAccessList (paID, peID, attributePermission, uName, uEmail, uPassword, uAvatar, uTimezone, uDefaultLanguage) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);
            }
        }

        if (is_array($args['akIDInclude'])) {
            foreach ($args['akIDInclude'] as $peID => $akIDs) {
                foreach ($akIDs as $akID) {
                    $v = [$this->getPermissionAccessID(), $peID, $akID];
                    $db->executeQuery('insert into UserPermissionEditPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
                }
            }
        }

        if (is_array($args['akIDExclude'])) {
            foreach ($args['akIDExclude'] as $peID => $akIDs) {
                foreach ($akIDs as $akID) {
                    $v = [$this->getPermissionAccessID(), $peID, $akID];
                    $db->executeQuery('insert into UserPermissionEditPropertyAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
                }
            }
        }
    }

    public function getAccessListItems($accessType = UserPermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = [], $checkCache = true)
    {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        $list = PermissionDuration::filterByActive($list);
        foreach ($list as $l) {
            $attributePermission = null;
            $pe = $l->getAccessEntityObject();
            $prow = $db->fetchAssoc('select attributePermission, uName, uPassword, uEmail, uAvatar, uTimezone, uDefaultLanguage from UserPermissionEditPropertyAccessList where peID = ? and paID = ?', [$pe->getAccessEntityID(), $this->getPermissionAccessID()]);
            if (is_array($prow) && $prow['attributePermission']) {
                $l->setAttributesAllowedPermission($prow['attributePermission']);
                $l->setAllowEditUserName($prow['uName']);
                $l->setAllowEditEmail($prow['uEmail']);
                $l->setAllowEditPassword($prow['uPassword']);
                $l->setAllowEditAvatar($prow['uAvatar']);
                $l->setAllowEditTimezone($prow['uTimezone']);
                $l->setAllowEditDefaultLanguage($prow['uDefaultLanguage']);
                $attributePermission = $prow['attributePermission'];
            } elseif ($l->getAccessType() == UserPermissionKey::ACCESS_TYPE_INCLUDE) {
                $l->setAttributesAllowedPermission('A');
                $l->setAllowEditUserName(1);
                $l->setAllowEditEmail(1);
                $l->setAllowEditPassword(1);
                $l->setAllowEditAvatar(1);
                $l->setAllowEditTimezone(1);
                $l->setAllowEditDefaultLanguage(1);
            } else {
                $l->setAttributesAllowedPermission('N');
                $l->setAllowEditUserName(0);
                $l->setAllowEditEmail(0);
                $l->setAllowEditPassword(0);
                $l->setAllowEditAvatar(0);
                $l->setAllowEditTimezone(0);
                $l->setAllowEditDefaultLanguage(0);
            }
            if ($attributePermission == 'C') {
                $akIDs = $db->GetCol('select akID from UserPermissionEditPropertyAttributeAccessListCustom where peID = ? and paID = ?', [$pe->getAccessEntityID(), $this->getPermissionAccessID()]);
                $l->setAttributesAllowedArray($akIDs);
            }
        }

        return $list;
    }
}
