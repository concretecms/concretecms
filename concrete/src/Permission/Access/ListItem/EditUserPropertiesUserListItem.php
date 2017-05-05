<?php
namespace Concrete\Core\Permission\Access\ListItem;

class EditUserPropertiesUserListItem extends UserListItem
{
    protected $customAttributeKeyArray = [];
    protected $attributesAllowedPermission = 'N';
    protected $allowEditUName = 0;
    protected $allowEditUEmail = 0;
    protected $allowEditUPassword = 0;
    protected $allowEditUAvatar = 0;
    protected $allowEditTimezone = 0;
    protected $allowEditDefaultLanguage = 0;

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

    public function setAllowEditUserName($allow)
    {
        $this->allowEditUName = $allow;
    }

    public function allowEditUserName()
    {
        return $this->allowEditUName;
    }

    public function setAllowEditEmail($allow)
    {
        $this->allowEditUEmail = $allow;
    }

    public function allowEditEmail()
    {
        return $this->allowEditUEmail;
    }

    public function setAllowEditPassword($allow)
    {
        $this->allowEditUPassword = $allow;
    }

    public function allowEditPassword()
    {
        return $this->allowEditUPassword;
    }

    public function setAllowEditAvatar($allow)
    {
        $this->allowEditUAvatar = $allow;
    }

    public function allowEditAvatar()
    {
        return $this->allowEditUAvatar;
    }

    public function setAllowEditTimezone($allow)
    {
        $this->allowEditUTimezone = $allow;
    }

    public function allowEditTimezone()
    {
        return isset($this->allowEditUTimezone) ? $this->allowEditUTimezone : null;
    }

    public function setAllowEditDefaultLanguage($allow)
    {
        $this->allowEditUDefaultLanguage = $allow;
    }

    public function allowEditDefaultLanguage()
    {
        return isset($this->allowEditUDefaultLanguage) ? $this->allowEditUDefaultLanguage : null;
    }
}
