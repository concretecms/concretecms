<?php
namespace Concrete\Core\Permission\Access\ListItem;

class EditPageThemePageListItem extends PageListItem
{
    protected $customThemeArray = array();
    protected $themesAllowedPermission = 'N';

    public function setThemesAllowedPermission($permission)
    {
        $this->themesAllowedPermission = $permission;
    }
    public function getThemesAllowedPermission()
    {
        return $this->themesAllowedPermission;
    }
    public function setThemesAllowedArray($pThemeIDs)
    {
        $this->customThemeArray = $pThemeIDs;
    }
    public function getThemesAllowedArray()
    {
        return $this->customThemeArray;
    }
}
