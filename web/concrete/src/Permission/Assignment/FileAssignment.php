<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\File\Set\Set;
use Concrete\Core\Permission\Key\Key;
use PermissionAccess;
use FileSet;
use Concrete\Core\File\File;
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
        $db = Database::connection();
        if ($this->permissionObjectToCheck instanceof File) {
            $r = $db->GetOne(
                'select paID from FilePermissionAssignments where fID = ? and pkID = ?',
                array(
                    $this->permissionObject->getFileID(),
                    $this->pk->getPermissionKeyID(),
                )
            );
            if ($r) {
                return PermissionAccess::getByID($r, $this->pk, false);
            }
        } else if (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $pk = Key::getByHandle($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]);
            $pk->setPermissionObject($this->permissionObjectToCheck);
            $pae = $pk->getPermissionAccessObject();
            return $pae;
        }
    }


    public function clearPermissionAssignment()
    {
        $db = Database::connection();
        $db->Execute('update FilePermissionAssignments set paID = 0 where pkID = ? and fID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getFileID()));
    }

    public function assignPermissionAccess(PermissionAccess $pa)
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
