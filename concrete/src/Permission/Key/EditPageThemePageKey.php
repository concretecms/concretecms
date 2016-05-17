<?php
namespace Concrete\Core\Permission\Key;

use Loader;
use User;
use Concrete\Core\Permission\Duration as PermissionDuration;

class EditPageThemePageKey extends PageKey
{
    protected function getAllowedThemeIDs()
    {
        $u = new User();
        $pae = $this->getPermissionAccessObject();
        if (!is_object($pae)) {
            return array();
        }

        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(PageKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);

        $db = Loader::db();
        $allpThemeIDs = $db->GetCol('select pThemeID from PageThemes order by pThemeID asc');
        $pThemeIDs = array();
        foreach ($list as $l) {
            if ($l->getThemesAllowedPermission() == 'N') {
                $pThemeIDs = array();
            }
            if ($l->getThemesAllowedPermission() == 'C') {
                if ($l->getAccessType() == PageKey::ACCESS_TYPE_EXCLUDE) {
                    $pThemeIDs = array_values(array_diff($pThemeIDs, $l->getThemesAllowedArray()));
                } else {
                    $pThemeIDs = array_unique(array_merge($pThemeIDs, $l->getThemesAllowedArray()));
                }
            }
            if ($l->getThemesAllowedPermission() == 'A') {
                $pThemeIDs = $allpThemeIDs;
            }
        }

        return $pThemeIDs;
    }

    public function validate($theme = false)
    {
        $u = new User();
        if ($u->isSuperUser()) {
            return true;
        }

        $themes = $this->getAllowedThemeIDs();
        if ($theme != false) {
            return in_array($theme->getThemeID(), $themes);
        } else {
            return count($themes) > 0;
        }
    }
}
