<?php

namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Key\FileSetKey as FileSetPermissionKey;
use Database;

class AddFileFileSetAccess extends FileSetAccess
{
    public function getAccessListItems(
        $accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE,
        $filterEntities = array()
    ) {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            $permission = $db->fetchColumn(
                'SELECT permission FROM FileSetPermissionFileTypeAccessList WHERE peID = ? AND paID = ?',
                array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
            if ($permission != 'N' && $permission != 'C') {
                $permission = 'A';
            }
            $l->setFileTypesAllowedPermission($permission);
            if ($permission == 'C') {
                $extensions = $db->GetCol(
                    'SELECT extension FROM FileSetPermissionFileTypeAccessListCustom WHERE peID = ? AND paID = ?',
                    array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
                $l->setFileTypesAllowedArray($extensions);
            }
        }

        return $list;
    }

    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $db = Database::connection();
        $r = $db->executeQuery(
            'SELECT * FROM FileSetPermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery(
                'INSERT INTO FileSetPermissionFileTypeAccessList (peID, paID, permission) VALUES (?, ?, ?)',
                $v);
        }
        $r = $db->executeQuery(
            'SELECT * FROM FileSetPermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['extension']);
            $db->executeQuery(
                'INSERT INTO FileSetPermissionFileTypeAccessListCustom  (peID, paID, extension) VALUES (?, ?, ?)',
                $v);
        }

        return $newPA;
    }

    public function save($args = array())
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery(
            'DELETE FROM FileSetPermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        $db->executeQuery(
            'DELETE FROM FileSetPermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        if (is_array($args['fileTypesIncluded'])) {
            foreach ($args['fileTypesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery(
                    'INSERT INTO FileSetPermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['fileTypesExcluded'])) {
            foreach ($args['fileTypesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery(
                    'INSERT INTO FileSetPermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['extensionInclude'])) {
            foreach ($args['extensionInclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->executeQuery(
                        'INSERT INTO FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }

        if (is_array($args['extensionExclude'])) {
            foreach ($args['extensionExclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->executeQuery(
                        'INSERT INTO FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }
    }
}
