<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Key\FileSetKey as FileSetPermissionKey;
use Loader;

class AddFileFileSetAccess extends FileSetAccess
{

    public function getAccessListItems(
        $accessType = FileSetPermissionKey::ACCESS_TYPE_INCLUDE,
        $filterEntities = array()
    ) {
        $db = Loader::db();
        $list = parent::getAccessListItems($accessType, $filterEntities);
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            $permission = $db->GetOne(
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
        $db = Loader::db();
        $r = $db->Execute(
            'SELECT * FROM FileSetPermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
            $db->Execute(
                'INSERT INTO FileSetPermissionFileTypeAccessList (peID, paID, permission) VALUES (?, ?, ?)',
                $v);
        }
        $r = $db->Execute(
            'SELECT * FROM FileSetPermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        while ($row = $r->FetchRow()) {
            $v = array($row['peID'], $newPA->getPermissionAccessID(), $row['extension']);
            $db->Execute(
                'INSERT INTO FileSetPermissionFileTypeAccessListCustom  (peID, paID, extension) VALUES (?, ?, ?)',
                $v);
        }
        return $newPA;
    }

    public function save($args)
    {
        parent::save();
        $db = Loader::db();
        $db->Execute(
            'DELETE FROM FileSetPermissionFileTypeAccessList WHERE paID = ?',
            array($this->getPermissionAccessID()));
        $db->Execute(
            'DELETE FROM FileSetPermissionFileTypeAccessListCustom WHERE paID = ?',
            array($this->getPermissionAccessID()));
        if (is_array($args['fileTypesIncluded'])) {
            foreach ($args['fileTypesIncluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->Execute(
                    'INSERT INTO FileSetPermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['fileTypesExcluded'])) {
            foreach ($args['fileTypesExcluded'] as $peID => $permission) {
                $v = array($this->getPermissionAccessID(), $peID, $permission);
                $db->Execute(
                    'INSERT INTO FileSetPermissionFileTypeAccessList (paID, peID, permission) VALUES (?, ?, ?)',
                    $v);
            }
        }

        if (is_array($args['extensionInclude'])) {
            foreach ($args['extensionInclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->Execute(
                        'INSERT INTO FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }

        if (is_array($args['extensionExclude'])) {
            foreach ($args['extensionExclude'] as $peID => $extensions) {
                foreach ($extensions as $extension) {
                    $v = array($this->getPermissionAccessID(), $peID, $extension);
                    $db->Execute(
                        'INSERT INTO FileSetPermissionFileTypeAccessListCustom (paID, peID, extension) VALUES (?, ?, ?)',
                        $v);
                }
            }
        }
    }

}
