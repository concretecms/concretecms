<?php
namespace Concrete\Core\Permission\Access\ListItem;

class AddFileFileFolderListItem extends FileFolderListItem
{
    protected $customFileTypesArray = array();
    protected $fileTypesAllowedPermission = 'N';

    public function setFileTypesAllowedPermission($permission)
    {
        $this->fileTypesAllowedPermission = $permission;
    }
    public function getFileTypesAllowedPermission()
    {
        return $this->fileTypesAllowedPermission;
    }
    public function setFileTypesAllowedArray($extensions)
    {
        $this->customFileTypesArray = $extensions;
    }
    public function getFileTypesAllowedArray()
    {
        return $this->customFileTypesArray;
    }
}
