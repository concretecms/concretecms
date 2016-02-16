<?php
namespace Concrete\Core\Permission\Access\ListItem;

class EditPagePropertiesPageListItem extends PageListItem
{
    protected $customAttributeKeyArray = array();
    protected $attributesAllowedPermission = 'N';
    protected $allowEditName = 0;
    protected $allowEditDateTime = 0;
    protected $allowEditUID = 0;
    protected $allowEditDescription = 0;
    protected $allowEditPaths = 0;

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
        $this->customAttributeKeyArray = $akIDs;
    }
    public function getAttributesAllowedArray()
    {
        return $this->customAttributeKeyArray;
    }

    public function setAllowEditName($allow)
    {
        $this->allowEditName = $allow;
    }

    public function allowEditName()
    {
        return $this->allowEditName;
    }

    public function setAllowEditDateTime($allow)
    {
        $this->allowEditDateTime = $allow;
    }

    public function allowEditDateTime()
    {
        return $this->allowEditDateTime;
    }

    public function setAllowEditUserID($allow)
    {
        $this->allowEditUID = $allow;
    }

    public function allowEditUserID()
    {
        return $this->allowEditUID;
    }

    public function setAllowEditDescription($allow)
    {
        $this->allowEditDescription = $allow;
    }

    public function allowEditDescription()
    {
        return $this->allowEditDescription;
    }

    public function setAllowEditPaths($allow)
    {
        $this->allowEditPaths = $allow;
    }

    public function allowEditPaths()
    {
        return $this->allowEditPaths;
    }
}
