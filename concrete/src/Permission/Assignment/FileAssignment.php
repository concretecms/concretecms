<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\File\Set\Set;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Access\Access;
use FileSet;
use Concrete\Core\Entity\File\File;
use Database;

class FileAssignment extends TreeNodeAssignment
{
    protected $permissionObjectToCheck;

    /**
     * @param File $f
     */
    public function setPermissionObject($f)
    {
        $this->permissionObject = $f;

        if ($f->overrideFileFolderPermissions()) {
            $this->permissionObjectToCheck = $f;
        } else {
            $this->permissionObjectToCheck = $f->getFileFolderObject();
        }
    }


    protected $inheritedPermissions = array(
        'view_file' => 'view_file_folder_file',
        'view_file_in_file_manager' => 'search_file_folder',
        'edit_file_properties' => 'edit_file_folder_file_properties',
        'edit_file_contents' => 'edit_file_folder_file_contents',
        'copy_file' => 'copy_file_folder_files',
        'edit_file_permissions' => 'edit_file_folder_permissions',
        'delete_file' => 'delete_file_folder_files',
    );

    public function getPermissionAccessObject()
    {

        $cache = \Core::make('cache/request');
        $identifier = sprintf('permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Database::connection();
        $r = null;
        if ($this->permissionObjectToCheck instanceof File) {
            $r = $db->GetOne(
                'select paID from FilePermissionAssignments where fID = ? and pkID = ?',
                array(
                    $this->permissionObject->getFileID(),
                    $this->pk->getPermissionKeyID(),
                )
            );
        } else if (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $inheritedPKID = $db->GetOne('select pkID from PermissionKeys where pkHandle = ?', array($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]));
            $r = $db->GetOne(
                'select paID from TreeNodePermissionAssignments where treeNodeID = ? and pkID = ?',
                array(
                    $this->permissionObjectToCheck->getTreeNodePermissionsNodeID(),
                    $inheritedPKID,
                )
            );
        }

        $pa = null;
        if ($r) {
            $pa = Access::getByID($r, $this->pk, false);
        }

        $cache->save($item->set($pa));
        return $pa;

    }


    public function clearPermissionAssignment()
    {
        $db = Database::connection();
        $db->Execute('update FilePermissionAssignments set paID = 0 where pkID = ? and fID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getFileID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Database::connection();
        $db->Replace('FilePermissionAssignments', array('fID' => $this->getPermissionObject()->getFileID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('fID', 'pkID'), true);
        $pa->markAsInUse();
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        return Assignment::getPermissionKeyToolsURL($task) . '&fID=' . $this->getPermissionObject()->getFileID();
    }

}
