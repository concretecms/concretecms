<?php

namespace Concrete\Core\Permission\Key;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Assignment\AreaAssignment;
use Concrete\Core\Permission\Duration as PermissionDuration;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use PDO;

class AddBlockToAreaAreaKey extends AreaKey
{
    /**
     * Operation identifier: adding a new block to the area.
     *
     * @var string
     */
    const OPERATION_NEWBLOCK = 'new-block';

    /**
     * Operation identifier: moving an existing block from another area.
     *
     * @var string
     */
    const OPERATION_EXISTINGBLOCK = 'existing-block';

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
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|\Concrete\Core\Block\Block $blockTypeOrBlock specify a block type when adding a new block, a block instance when adding an existing block.
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Key\Key::validate()
     */
    public function validate($blockTypeOrBlock = false)
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        if ($u->isSuperUser()) {
            return true;
        }
        if ($blockTypeOrBlock instanceof Block) {
            $types = $this->getAllowedBlockTypeIDsFor(static::OPERATION_EXISTINGBLOCK);
            $blockType = $blockTypeOrBlock->getBlockTypeObject();
        } else {
            $types = $this->getAllowedBlockTypeIDsFor(static::OPERATION_NEWBLOCK);
            $blockType = $blockTypeOrBlock;
        }

        return $blockType ? in_array($blockType->getBlockTypeID(), $types) : !empty($types);
    }

    /**
     * @deprecated Use getAllowedBlockTypeIDsFor(AddBlockToAreaAreaKey::OPERATION_NEWBLOCK)
     *
     * @return int[]
     */
    protected function getAllowedBlockTypeIDs()
    {
        return $this->getAllowedBlockTypeIDsFor(static::OPERATION_NEWBLOCK);
    }

    /**
     * Get the list of allowed block type IDs for a specific operation.
     *
     * @param string $operation One of the OPERATION_... constants.
     *
     * @return int[]
     */
    protected function getAllowedBlockTypeIDsFor($operation)
    {
        $pae = $this->getAreaPermissionAccessObject();
        if (!is_object($pae)) {
            return [];
        }
        if (!in_array($operation, [static::OPERATION_NEWBLOCK, static::OPERATION_EXISTINGBLOCK], true)) {
            $operation = static::OPERATION_NEWBLOCK;
        }
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        $accessEntities = $u->getUserAccessEntityObjects();
        $accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
        $list = $this->getAreaAccessListItems(AreaKey::ACCESS_TYPE_ALL, $accessEntities);
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
                $identifier = 'blocktypeids/public/' . $operation;
            }
            $item = $cache->getItem($identifier);
            if ($item->isMiss()) {
                $allBTIDs = [];
                $params = [];
                if ($dsh->inDashboard()) {
                    $sql = 'select btID from BlockTypes';
                } else {
                    $sql = 'select btID from BlockTypes where btIsInternal = 0';
                    if ($operation === static::OPERATION_EXISTINGBLOCK) {
                        $sql .= ' or btHandle = ?';
                        $params[] = BLOCK_HANDLE_STACK_PROXY;
                    }
                }
                $rs = $db->executeQuery($sql, $params);
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
