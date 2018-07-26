<?php

namespace Concrete\Core\Block\BlockType;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;

class Set extends ConcreteObject
{
    /**
     * Get a block type set given its ID.
     *
     * @param int $btsID
     *
     * @return static|null
     */
    public static function getByID($btsID)
    {
        $result = null;
        $btsID = (int) $btsID;
        if ($btsID !== 0) {
            $app = Application::getFacadeApplication();
            $cache = $app->make('cache/request');
            $identifier = sprintf('block/type/set/%s', $btsID);
            $item = $cache->getItem($identifier);
            if ($item->isMiss()) {
                $item->lock();
                $db = $app->make(Connection::class);
                $row = $db->fetchAssoc('select btsID, btsHandle, pkgID, btsName from BlockTypeSets where btsID = ?', [$btsID]);
                if ($row !== false) {
                    $result = new static();
                    $result->setPropertiesFromArray($row);
                }
                $item->set($result)->save();
            } else {
                $result = $item->get();
            }
        }

        return $result;
    }

    /**
     * Get a block type set given its handle.
     *
     * @param string $btsHandle
     *
     * @return static|null
     */
    public static function getByHandle($btsHandle)
    {
        $result = null;
        $btsHandle = (string) $btsHandle;
        if ($btsHandle !== '') {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $row = $db->fetchAssoc('select btsID, btsHandle, pkgID, btsName from BlockTypeSets where btsHandle = ?', [$btsHandle]);
            if ($row !== false) {
                $result = new static();
                $result->setPropertiesFromArray($row);
            }
        }

        return $result;
    }

    /**
     * Get the list of block type sets defined by a package.
     *
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package|int $pkg
     *
     * @return static[]
     */
    public static function getListByPackage($pkg)
    {
        $result = [];
        $pkgID = (int) (is_object($pkg) ? $pkg->getPackageID() : $pkg);
        if ($pkgID !== 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $rs = $db->executeQuery('select btsID from BlockTypeSets where pkgID = ? order by btsID asc', [$pkgID]);
            while (($btsID = $rs->fetchColumn()) !== false) {
                $result[] = static::getByID($btsID);
            }
        }

        return $result;
    }

    /**
     * Get the list of block type sets.
     *
     * @param string[] $excluded a list of block type set handles to be exluded
     *
     * @return static[]
     */
    public static function getList($excluded = ['core_desktop'])
    {
        $result = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if (empty($excluded)) {
            $rs = $db->executeQuery('select btsID from BlockTypeSets order by btsDisplayOrder asc');
        } else {
            $rs = $db->executeQuery('select btsID from BlockTypeSets where btsHandle not in (?) order by btsDisplayOrder asc',
                [$excluded],
                [Connection::PARAM_STR_ARRAY]
            );
        }
        while (($btsID = $rs->fetchColumn()) !== false) {
            $result[] = static::getByID($btsID);
        }

        return $result;
    }

    /**
     * Create a new block type set.
     *
     * @param string $btsHandle
     * @param string $btsName
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package|int|false $pkg
     *
     * @return static
     */
    public static function add($btsHandle, $btsName, $pkg = false)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $pkgID = (int) (is_object($pkg) ? $pkg->getPackageID() : $pkg);
        $displayOrder = $db->fetchColumn('select max(btsDisplayOrder) from BlockTypeSets');
        if ($displayOrder === null) {
            $displayOrder = 0;
        } else {
            ++$displayOrder;
        }
        $db->insert(
            'BlockTypeSets',
            [
                'btsHandle' => (string) $btsHandle,
                'btsName' => (string) $btsName,
                'pkgID' => $pkgID,
            ]
        );
        $id = $db->lastInsertId();

        $bs = static::getByID($id);

        return $bs;
    }

    /**
     * Export all the block type sets to a SimpleXMLElement element.
     *
     * @param \SimpleXMLElement $xml The parent SimpleXMLElement element
     */
    public static function exportList($xml)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $bxml = $xml->addChild('blocktypesets');
        $rs = $db->executeQuery('select btsID from BlockTypeSets order by btsID asc');
        while (($btsID = $rs->fetchColumn()) !== false) {
            $bts = static::getByID($btsID);
            $bts->export($bxml);
        }
    }

    /**
     * Get the list of block types that don't belong to any block type set.
     *
     * @param bool $includeInternalBlockTypes
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType[]
     */
    public static function getUnassignedBlockTypes($includeInternalBlockTypes = false)
    {
        $result = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery(<<<EOT
select BlockTypes.btID
from BlockTypes
left join BlockTypeSetBlockTypes on BlockTypes.btID = BlockTypeSetBlockTypes.btID
left join BlockTypeSets on BlockTypeSetBlockTypes.btsID = BlockTypeSets.btsID
where BlockTypeSets.btsID is null
order by BlockTypes.btDisplayOrder asc
EOT
        );
        while (($btID = $rs->fetchColumn()) !== false) {
            $bt = BlockType::getByID($btID);
            if ($bt !== null) {
                if ($includeInternalBlockTypes || !$bt->isBlockTypeInternal()) {
                    $result[] = $bt;
                }
            }
        }

        return $result;
    }

    /**
     * Get the block type set ID.
     *
     * @return int
     */
    public function getBlockTypeSetID()
    {
        return $this->btsID;
    }

    /**
     * Get the block type set handle.
     *
     * @return string
     */
    public function getBlockTypeSetHandle()
    {
        return $this->btsHandle;
    }

    /**
     * Get the block type set name.
     *
     * @return string
     */
    public function getBlockTypeSetName()
    {
        return $this->btsName;
    }

    /**
     * Get the block type set name (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getBlockTypeSetDisplayName($format = 'html')
    {
        $value = tc('BlockTypeSetName', $this->getBlockTypeSetName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Get the ID of the package that defined this set.
     *
     * @return int|null
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the handle of the package that defined this set.
     *
     * @return string|false
     */
    public function getPackageHandle()
    {
        $pkgID = $this->getPackageID();

        return empty($pkgID) ? false : PackageList::getHandle($pkgID);
    }

    /**
     * Update the name of this set.
     *
     * @param string $btsName
     */
    public function updateBlockTypeSetName($btsName)
    {
        $btsName = (string) $btsName;
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->update('BlockTypeSets', ['btsName' => $btsName], ['btsID' => $this->getBlockTypeSetID()]);
        $this->btsName = $btsName;
    }

    /**
     * Update the handle of this set.
     *
     * @param string $btsHandle
     */
    public function updateBlockTypeSetHandle($btsHandle)
    {
        $btsHandle = (string) $btsHandle;
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->update('BlockTypeSets', ['btsHandle' => $btsHandle], ['btsID' => $this->getBlockTypeSetID()]);
        $this->btsHandle = $btsHandle;
    }

    /**
     * Update the display order of this set.
     *
     * @param int $displayOrder
     */
    public function updateBlockTypeSetDisplayOrder($displayOrder)
    {
        $displayOrder = (string) $displayOrder;
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->update('BlockTypeSets', ['btsDisplayOrder' => $displayOrder], ['btsID' => $this->getBlockTypeSetID()]);
    }

    /**
     * Associate a block type to this set.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt
     */
    public function addBlockType(BlockTypeEntity $bt)
    {
        if (!$this->contains($bt)) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $displayOrder = $db->fetchColumn('select max(displayOrder) from BlockTypeSetBlockTypes where btsID = ?', [$this->getBlockTypeSetID()]);
            if ($displayOrder === null) {
                $displayOrder = 0;
            } else {
                ++$displayOrder;
            }
            $db->insert(
                'BlockTypeSetBlockTypes',
                [
                    'btsID' => $this->getBlockTypeSetID(),
                    'btID' => $bt->getBlockTypeID(),
                    'displayOrder' => $displayOrder,
                ]
            );
        }
    }

    /**
     * Update the display order of a block type contained in this set.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt The block type to be updated
     * @param int $displayOrder the new display order of the blocktype inside this set
     */
    public function setBlockTypeDisplayOrder(BlockTypeEntity $bt, $displayOrder)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->update(
            'BlockTypeSetBlockTypes',
            ['displayOrder' => $displayOrder],
            ['btID' => $bt->getBlockTypeID(), 'btsID' => $this->getBlockTypeSetID()]
        );
    }

    /**
     * Disassociate all the block types currently associated to this set.
     */
    public function clearBlockTypes()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->delete('BlockTypeSetBlockTypes', ['btsID' => $this->getBlockTypeSetID()]);
    }

    /**
     * Dissociate a specific block type currently associated to this set.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|int $bt A block type (or its ID)
     */
    public function deleteKey($bt)
    {
        $btID = (int) (is_object($bt) ? $bt->getBlockTypeID() : $bt);
        if ($btID !== 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $db->delete('BlockTypeSetBlockTypes', ['btsID' => $this->getBlockTypeSetID(), 'btID' => $btID]);
            $this->rescanDisplayOrder();
        }
    }

    /**
     * Export this set to a SimpleXMLElement element.
     *
     * @param \SimpleXMLElement $axml The parent SimpleXMLElement element
     *
     * @return \SimpleXMLElement returns the newly created SimpleXMLElement element representing this set
     */
    public function export($axml)
    {
        $bset = $axml->addChild('blocktypeset');
        $bset->addAttribute('handle', $this->getBlockTypeSetHandle());
        $bset->addAttribute('name', $this->getBlockTypeSetName());
        $bset->addAttribute('package', $this->getPackageHandle());
        $types = $this->getBlockTypes();
        foreach ($types as $bt) {
            $typenode = $bset->addChild('blocktype');
            $typenode->addAttribute('handle', $bt->getBlockTypeHandle());
        }

        return $bset;
    }

    /**
     * Get the list of block types associated to this set.
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType[]
     */
    public function getBlockTypes()
    {
        $result = [];
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery('select btID from BlockTypeSetBlockTypes where btsID = ? order by displayOrder asc', [$this->getBlockTypeSetID()]);
        while (($btID = $rs->fetchColumn()) !== false) {
            $bt = BlockType::getByID($btID);
            if ($bt !== null) {
                $result[] = $bt;
            }
        }

        return $result;
    }

    /**
     * Does this set contain a block type?
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|int $bt A block type (or its ID)
     *
     * @return bool
     */
    public function contains($bt)
    {
        $result = false;
        $btID = (int) (is_object($bt) ? $bt->getBlockTypeID() : $bt);
        if ($btID !== 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $result = $db->fetchColumn('select btID from BlockTypeSetBlockTypes where btsID = ? and btID = ?', [$this->getBlockTypeSetID(), $btID]) !== false;
        }

        return $result;
    }

    /**
     * Get the display order of a block type as sorted for this set.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|int $bt A block type (or its ID)
     *
     * @return int|false Returns false if the block type is not associated to this set
     */
    public function displayOrder($bt)
    {
        $result = false;
        $btID = (int) (is_object($bt) ? $bt->getBlockTypeID() : $bt);
        if ($btID !== 0) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $displayOrder = $db->fetchColumn('select displayOrder from BlockTypeSetBlockTypes where btsID = ? and btID = ?', [$this->getBlockTypeSetID(), $btID]);
            if ($displayOrder !== false) {
                $result = (int) $displayOrder;
            }
        }

        return $result;
    }

    /**
     * Delete this set.
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->delete('BlockTypeSetBlockTypes', ['btsID' => $this->getBlockTypeSetID()]);
        $db->delete('BlockTypeSets', ['btsID' => $this->getBlockTypeSetID()]);
    }

    /**
     * @deprecated This method is an alias of getBlockTypes
     *
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType[]
     */
    public function get()
    {
        return $this->getBlockTypes();
    }

    /**
     * Regenereate the display order values of the block types associated to this set.
     */
    protected function rescanDisplayOrder()
    {
        $displayOrder = 0;
        $btsID = $this->getBlockTypeSetID();
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $rs = $db->executeQuery('select btID from BlockTypeSetBlockTypes where btsID = ? order by displayOrder asc', [$btsID]);
        while (($btID = $rs->fetchColumn()) !== false) {
            $db->update(
                'BlockTypeSetBlockTypes',
                [
                    'displayOrder' => $displayOrder,
                ],
                [
                    'btID' => $btID,
                    'btsID' => $btsID,
                ]
            );
            ++$displayOrder;
        }
    }
}
