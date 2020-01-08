<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class BlockCommand implements CommandInterface
{

    public function __construct($blockID, $pageID, $collectionVersionID, $areaHandle)
    {
        $this->pageID = $pageID;
        $this->blockID = $blockID;
        $this->areaHandle = $areaHandle;
        $this->collectionVersionID = $collectionVersionID;
    }


    protected $pageID;

    protected $blockID;

    protected $areaHandle;

    protected $collectionVersionID;

    /**
     * @return mixed
     */
    public function getPageID()
    {
        return $this->pageID;
    }

    /**
     * @param mixed $pageID
     */
    public function setPageID($pageID)
    {
        $this->pageID = $pageID;
    }

    /**
     * @return mixed
     */
    public function getBlockID()
    {
        return $this->blockID;
    }

    /**
     * @param mixed $blockID
     */
    public function setBlockID($blockID)
    {
        $this->blockID = $blockID;
    }

    /**
     * @return mixed
     */
    public function getAreaHandle()
    {
        return $this->areaHandle;
    }

    /**
     * @param mixed $areaHandle
     */
    public function setAreaHandle($areaHandle)
    {
        $this->areaHandle = $areaHandle;
    }

    /**
     * @return mixed
     */
    public function getCollectionVersionID()
    {
        return $this->collectionVersionID;
    }

    /**
     * @param mixed $collectionVersionID
     */
    public function setCollectionVersionID($collectionVersionID)
    {
        $this->collectionVersionID = $collectionVersionID;
    }
    
}