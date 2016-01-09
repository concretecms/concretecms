<?php

namespace Concrete\Core\Permission\Assignment;

use PermissionAccess;
use Core;
use Loader;

class PageAssignment extends Assignment
{
    public function getPermissionAccessObject()
    {
        $cache = Core::make('cache/request');
        $identifier = sprintf('permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Loader::db();
        $r = $db->GetOne('select paID from PagePermissionAssignments where cID = ? and pkID = ?', array($this->getPermissionObject()->getPermissionsCollectionID(), $this->pk->getPermissionKeyID()));
        $pa = $r ? PermissionAccess::getByID($r, $this->pk, false) : null;

        $item->set($pa);

        return $pa;
    }

    public function clearPermissionAssignment()
    {
        $db = Loader::db();
        $db->Execute('update PagePermissionAssignments set paID = 0 where pkID = ? and cID = ?', array($this->pk->getPermissionKeyID(), $this->getPermissionObject()->getPermissionsCollectionID()));

        $cache = Core::make('cache/request');
        $identifier = sprintf('permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $cache->delete($identifier);
    }

    public function assignPermissionAccess(PermissionAccess $pa)
    {
        $db = Loader::db();
        $db->Replace('PagePermissionAssignments', array('cID' => $this->getPermissionObject()->getPermissionsCollectionID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('cID', 'pkID'), true);
        $pa->markAsInUse();

        $cache = Core::make('cache/request');
        $identifier = sprintf('permission/assignment/access/%s/%s',
            $this->pk->getPermissionKeyHandle(),
            $this->getPermissionObject()->getPermissionObjectIdentifier()
        );
        $cache->delete($identifier);
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        $pageArray = $this->pk->getMultiplePageArray();
        if (is_array($pageArray) && count($pageArray) > 0) {
            $cIDStr = '';
            foreach ($pageArray as $sc) {
                $cIDStr .= '&cID[]='.$sc->getCollectionID();
            }

            return parent::getPermissionKeyToolsURL($task).$cIDStr;
        } else {
            return parent::getPermissionKeyToolsURL($task).'&cID='.$this->getPermissionObject()->getCollectionID();
        }
    }
}
