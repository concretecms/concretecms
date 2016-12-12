<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Key\FileFolderKey;
use Database;

class AddFileFileFolderAccess extends FileFolderAccess
{
    public function getAccessListItems(
        $accessType = FileFolderKey::ACCESS_TYPE_INCLUDE,
        $filterEntities = array(), $checkCache = true
    ) {
        $db = Database::connection();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            $permission = $db->fetchColumn(
                'SELECT permission FROM FilePermissionFileTypeAccessList WHERE peID = ? AND paID = ?',
                array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
            if ($permission != 'N' && $permission != 'C') {
                $permission = 'A';
            }
            $l->setFileTypesAllowedPermission($permission);
            if ($permission == 'C') {
                $extensions = $db->GetCol(
                    'SELECT extension FROM FilePermissionFileTypeAccessListCustom WHERE peID = ? AND paID = ?',
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
            'SELECT * FROM FilePermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->executeQuery(
                'INSERT INTO FilePermissionFileTypeAccessList (peID, paID, permission) VALUES (?, ?, ?)',
                $v);
        }
        $r = $db->executeQuery(
            'SELECT * FROM FilePermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['extension']);
            $db->executeQuery(
                'INSERT INTO FilePermissionFileTypeAccessListCustom  (peID, paID, extension) VALUES (?, ?, ?)',
                $v);
        }

        return $newPA;
    }

    public function save($args = array())
    {
        parent::save();
        $db = Database::connection();
        $db->executeQuery(
            'DELETE FROM FilePermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        $db->executeQuery(
            'DELETE FROM FilePermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        if (is_array($args['fileTypesIncluded'])) {
            foreach ($args['fileTypesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery(
                    'INSERT INTO FilePermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['fileTypesExcluded'])) {
            foreach ($args['fileTypesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->executeQuery(
                    'INSERT INTO FilePermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['extensionInclude'])) {
            foreach ($args['extensionInclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->executeQuery(
                        'INSERT INTO FilePermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }

        if (is_array($args['extensionExclude'])) {
            foreach ($args['extensionExclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->executeQuery(
                        'INSERT INTO FilePermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }
    }
}
