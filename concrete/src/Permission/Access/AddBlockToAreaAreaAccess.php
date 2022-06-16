<?php

namespace Concrete\Core\Permission\Access;

use Concrete\Core\Area\Area;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Support\Facade\Application;
use PDO;

class AddBlockToAreaAreaAccess extends AreaAccess
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Access\Access::duplicate()
     */
    public function duplicate($newPA = false)
    {
        $newPA = parent::duplicate($newPA);
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $r = $db->executeQuery('select * from AreaPermissionBlockTypeAccessList where paID = ?', [$this->getPermissionAccessID()]);
        while (($row = $r->fetch(PDO::FETCH_ASSOC)) !== false) {
            $db->insert('AreaPermissionBlockTypeAccessList', [
                'peID' => $row['peID'],
                'paID' => $newPA->getPermissionAccessID(),
                'permission' => $row['permission'],
            ]);
        }
        $r = $db->executeQuery('select * from AreaPermissionBlockTypeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        while (($row = $r->fetch(PDO::FETCH_ASSOC)) !== false) {
            $db->insert('AreaPermissionBlockTypeAccessListCustom', [
                'peID' => $row['peID'],
                'paID' => $newPA->getPermissionAccessID(),
                'btID' => $row['btID'],
            ]);
        }

        return $newPA;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Access\Access::getAccessListItems()
     */
    public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = [], $checkCache = true)
    {
        $app = Application::getFacadeApplication();
        if ($checkCache) {
            $cache = $app->make('cache/request');
            $item = $cache->getItem($this->getCacheIdentifier($accessType, $filterEntities));
            if (!$item->isMiss()) {
                return $item->get();
            }
            $item->lock();
        }
        $db = $app->make(Connection::class);
        $list = parent::getAccessListItems($accessType, $filterEntities, false);
        $pobj = $this->getPermissionObjectToCheck();
        foreach ($list as $l) {
            $pe = $l->getAccessEntityObject();
            if ($pobj instanceof Page) {
                $permission = $db->fetchColumn('select permission from BlockTypePermissionBlockTypeAccessList where paID = ?', [$l->getPermissionAccessID()]);
            } else {
                $permission = $db->fetchColumn('select permission from AreaPermissionBlockTypeAccessList where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
            }
            if ($permission != 'N' && $permission != 'C') {
                $permission = 'A';
            }
            $l->setBlockTypesAllowedPermission($permission);
            if ($permission == 'C') {
                if ($pobj instanceof Area) {
                    $rs = $db->executeQuery('select btID from AreaPermissionBlockTypeAccessListCustom where peID = ? and paID = ?', [$pe->getAccessEntityID(), $l->getPermissionAccessID()]);
                } else {
                    $rs = $db->executeQuery('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ?', [$l->getPermissionAccessID()]);
                }
                $btIDs = [];
                while (($btID = $rs->fetchColumn()) !== false) {
                    $btIDs[] = (int) $btID;
                }
                $l->setBlockTypesAllowedArray($btIDs);
            }
        }

        if ($checkCache) {
            $cache->save($item->set($list));
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Access\Access::save()
     */
    public function save($args = [])
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        parent::save($args);
        $db->executeQuery('delete from AreaPermissionBlockTypeAccessList where paID = ?', [$this->getPermissionAccessID()]);
        $db->executeQuery('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', [$this->getPermissionAccessID()]);
        if (isset($args['blockTypesIncluded']) && is_array($args['blockTypesIncluded'])) {
            foreach ($args['blockTypesIncluded'] as $peID => $permission) {
                $v = [$this->getPermissionAccessID(), $peID, $permission];
                $db->executeQuery('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (isset($args['blockTypesExcluded']) && is_array($args['blockTypesExcluded'])) {
            foreach ($args['blockTypesExcluded'] as $peID => $permission) {
                $v = [$this->getPermissionAccessID(), $peID, $permission];
                $db->executeQuery('insert into AreaPermissionBlockTypeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
            }
        }

        if (isset($args['btIDInclude']) && is_array($args['btIDInclude'])) {
            foreach ($args['btIDInclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = [$this->getPermissionAccessID(), $peID, $btID];
                    $db->executeQuery('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }

        if (isset($args['btIDExclude']) && is_array($args['btIDExclude'])) {
            foreach ($args['btIDExclude'] as $peID => $btIDs) {
                foreach ($btIDs as $btID) {
                    $v = [$this->getPermissionAccessID(), $peID, $btID];
                    $db->executeQuery('insert into AreaPermissionBlockTypeAccessListCustom (paID, peID, btID) values (?, ?, ?)', $v);
                }
            }
        }
    }
}
