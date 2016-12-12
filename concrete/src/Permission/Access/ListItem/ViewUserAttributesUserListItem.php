<?php
namespace Concrete\Core\Permission\Access\ListItem;

class ViewUserAttributesUserListItem extends UserListItem
{
    protected $customAttributeArray = array();
    protected $attributesAllowedPermission = 'N';

    public function setAttributesAllowedPermission($permission)
    {
        $this->attributesAllowedPermission = $permission;
    }
    public function getAttributesAllowedPermission()
    {
        return $this->attributesAllowedPermission;
    }
    public function setAttributesAllowedArray($akIDs)
    {
        $this->customAttributeArray = $akIDs;
    }
    public function getAttributesAllowedArray()
    {
        return $this->customAttributeArray;
    }
}
