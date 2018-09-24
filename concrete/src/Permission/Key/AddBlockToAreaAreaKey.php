<?php

namespace Concrete\Core\Permission\Key;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use PDO;

class AddBlockToAreaAreaKey extends AreaKey
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Key\AreaKey::copyFromPageToArea()
     */
    public function copyFromPageToArea()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $inheritedPKID = $db->fetchColumn('select pkID from PermissionKeys where pkHandle = ?', ['add_block']);
        $r = $db->executeQuery('select peID, pa.paID from PermissionAssignments pa inner join PermissionAccessList pal on pa.paID = pal.paID where pkID = ?', [$inheritedPKID]);
        while (($row = $r->fetch(PDO::FETCH_ASSOC)) !== false) {
            $db->replace(
                'AreaPermissionAssignments',
                [
                    'cID' => $this->permissionObject->getCollectionID(),
                    'arHandle' => $this->permissionObject->getAreaHandle(),
                    'pkID' => $this->getPermissionKeyID(),
                    'paID' => $row['paID'],
                ],
                ['cID', 'arHandle', 'pkID'],
                true
            );
            $rx = $db->executeQuery('select permission from BlockTypePermissionBlockTypeAccessList where paID = ? and peID = ?', [$row['paID'], $row['peID']]);
            while (($rowx = $rx->fetch(PDO::FETCH_ASSOC)) !== false) {
                $db->replace(
                    'AreaPermissionBlockTypeAccessList',
                    [
                        'peID' => $row['peID'],
                        'permission' => $rowx['permission'],
                        'paID' => $row['paID'],
                    ],
                    ['paID', 'peID'],
                    true
                );
            }
            $db->executeQuery('delete from AreaPermissionBlockTypeAccessListCustom where paID = ?', [$row['paID']]);
            $rx = $db->executeQuery('select btID from BlockTypePermissionBlockTypeAccessListCustom where paID = ? and peID = ?', [$row['paID'], $row['peID']]);
            while (($rowx = $rx->fetch(PDO::FETCH_ASSOC)) !== false) {
                $db->replace(
                    'AreaPermissionBlockTypeAccessListCustom',
                    [
                        'paID' => $row['paID'],
                        'btID' => $rowx['btID'],
                        'peID' => $row['peID'],
                    ],
                    ['paID', 'peID', 'btID'],
                    true
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Key\Key::validate()
     */
    public function validate($bt = false)
    {
        $u = new User();
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

    /**
     * @return int[]
     */
    protected function getAllowedBlockTypeIDs()
    {
        $u = new User();
        $pae = $this->getPermissionAccessObject();
        if (!is_object($pae)) {
            return [];
        }
        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAccessListItems(AreaKey::ACCESS_TYPE_ALL, $accessEntities);
        $list = PermissionDuration::filterByActive($list);
        $btIDs = [];
        if (count($list) > 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $cache = $app->make('cache/request');
            $dsh = $app->make('helper/concrete/dashboard');
            if ($dsh->inDashboard()) {
                $identifier = 'blocktypeids/all';
            } else {
                $identifier = 'blocktypeids/public';
            }
            $item = $cache->getItem($identifier);
            if ($item->isMiss()) {
                $allBTIDs = [];
                $sql = $dsh->inDashboard() ? 'select btID from BlockTypes' : 'select btID from BlockTypes where btIsInternal = 0';
                $rs = $db->executeQuery($sql);
                while (($btID = $rs->fetchColumn()) !== false) {
                    $allBTIDs[] = (int) $btID;
                }
                $item->set($allBTIDs)->save();
            } else {
                $allBTIDs = $item->get();
            }
            foreach ($list as $l) {
                switch ($l->getBlockTypesAllowedPermission()) {
                    case 'N':
                        $btIDs = [];
                        break;
                    case 'C':
                        if ($l->getAccessType() == AreaKey::ACCESS_TYPE_EXCLUDE) {
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
}
