<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class BlockCommand extends Command
{
    /**
     * @var int
     */
    protected $blockID;

    /**
     * @var int
     */
    protected $pageID;

    /**
     * @var int
     */
    protected $collectionVersionID;

    /**
     * @var string
     */
    protected $areaHandle;

    public function __construct(int $blockID, int $pageID, int $collectionVersionID, string $areaHandle)
    {
        $this
            ->setBlockID($blockID)
            ->setPageID($pageID)
            ->setCollectionVersionID($collectionVersionID)
            ->setAreaHandle($areaHandle)
        ;
    }

    public function getBlockID(): int
    {
        return $this->blockID;
    }

    /**
     * @return $this
     */
    public function setBlockID(int $blockID): object
    {
        $this->blockID = $blockID;

        return $this;
    }

    public function getPageID(): int
    {
        return $this->pageID;
    }

    /**
     * @return $this
     */
    public function setPageID(int $pageID): object
    {
        $this->pageID = $pageID;

        return $this;
    }

    public function getCollectionVersionID(): int
    {
        return $this->collectionVersionID;
    }

    /**
     * @return $this
     */
    public function setCollectionVersionID(int $collectionVersionID): object
    {
        $this->collectionVersionID = $collectionVersionID;

        return $this;
    }

    public function getAreaHandle(): string
    {
        return $this->areaHandle;
    }

    /**
     * @return $this
     */
    public function setAreaHandle(string $areaHandle): object
    {
        $this->areaHandle = $areaHandle;

        return $this;
    }
}
