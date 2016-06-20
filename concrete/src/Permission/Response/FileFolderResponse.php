<?php
namespace Concrete\Core\Permission\Response;

use User;
use FileSet;

class FileFolderResponse extends TreeNodeResponse
{

    public function canViewTreeNode()
    {
        return $this->validate('search_file_folder');
    }

    /**
     * @deprecated
     */
    public function canAccessFileManager()
    {
        return $this->canSearchFiles();
    }

    public function canSearchFiles()
    {
        return $this->validate('search_file_folder');
    }

    public function canDeleteTreeNode()
    {
        return $this->validate('delete_file_folder');
    }

    public function canAddTreeSubNode()
    {
        return $this->validate('add_file');
    }

    public function canDuplicateTreeNode()
    {
        return false;
    }

    public function canEditTreeNode()
    {
        return $this->validate('edit_file_folder');
    }

    public function canEditTreeNodePermissions()
    {
        return $this->validate('edit_file_folder_permissions');
    }

    public function canAddFiles()
    {
        return $this->validate('add_file');
    }

    /**
     * Returns all file extensions this user can add.
     */
    public function getAllowedFileExtensions()
    {
        $pk = $this->category->getPermissionKeyByHandle('add_file');
        $pk->setPermissionObject($this->object);
        $r = $pk->getAllowedFileExtensions();

        return $r;
    }

    public function canAddFileType($ext)
    {
        $pk = $this->category->getPermissionKeyByHandle('add_file');
        $pk->setPermissionObject($this->object);

        return $pk->validate($ext);
    }

    public function canDeleteFileSet()
    {
        $fs = $this->getPermissionObject();
        $u = new User();
        if ($fs->getFileSetType() == FileSet::TYPE_PRIVATE && $fs->getFileSetUserID() == $u->getUserID()) {
            return true;
        }

        return $this->validate('delete_file_set');
    }
}
