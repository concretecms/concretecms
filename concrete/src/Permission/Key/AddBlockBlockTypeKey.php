<?php
namespace Concrete\Core\Permission\Key;

use Loader;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Application;
use PermissionKey;
use Concrete\Core\Permission\Duration as PermissionDuration;

class AddBlockBlockTypeKey extends BlockTypeKey
{
    protected function getAllowedBlockTypeIDs()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        $pae = $this->getPermissionAccessObject();
        if (!is_object($pae)) {
            return array();
        }
        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);

        $db = Loader::db();
        $btIDs = array();
        if (count($list) > 0) {
            $dsh = Loader::helper('concrete/dashboard');
            if ($dsh->inDashboard()) {
                $allBTIDs = $db->GetCol('select btID from BlockTypes');
            } else {
                $allBTIDs = $db->GetCol('select btID from BlockTypes where btIsInternal = 0');
            }
            foreach ($list as $l) {
                switch ($l->getBlockTypesAllowedPermission()) {
                    case 'N':
                        $btIDs = array();
                        break;
                    case 'C':
                        if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
                            $btIDs = array_values(array_diff($btIDs, $l->getBlockTypesAllowedArray()));
                        } else {
                            $btIDs = array_unique(array_merge($btIDs, $l->getBlockTypesAllowedArray()));
                        }
                        break;
                    case 'A':
                        $btIDs = $allBTIDs;
                        break;
                }
            }
        }

        return $btIDs;
    }

    public function validate($bt = false)
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        if ($u->isSuperUser()) {
            return true;
        }

        $types = $this->getAllowedBlockTypeIDs();
        if ($bt != false) {
            return in_array($bt->getBlockTypeID(), $types);
        } else {
            return count($types) > 0;
        }
    }
}
