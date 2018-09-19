<?php

namespace Concrete\Core\Page\Type\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class UpdatePageTypeDefaultsCommand implements BatchableCommandInterface, QueueableCommand
{

    protected $pageTypeDefaultPageID;

    protected $pageID;

    protected $collectionVersionID;

    protected $blocksToUpdate;

    protected $blocksToAdd;

    /**
     * UpdatePageTypeDefaultsCommand constructor.
     * @param $pageID
     * @param $collectionVersionID
     * @param $blocksToUpdate
     * @param $blocksToAdd
     */
    public function __construct($pageTypeDefaultPageID, $pageID, $collectionVersionID, $blocksToUpdate, $blocksToAdd)
    {
        $this->pageTypeDefaultPageID = $pageTypeDefaultPageID;
        $this->pageID = $pageID;
        $this->collectionVersionID = $collectionVersionID;
        $this->blocksToUpdate = $blocksToUpdate;
        $this->blocksToAdd = $blocksToAdd;
    }

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

    /**
     * @return mixed
     */
    public function getBlocksToUpdate()
    {
        return $this->blocksToUpdate;
    }

    /**
     * @param mixed $blocksToUpdate
     */
    public function setBlocksToUpdate($blocksToUpdate)
    {
        $this->blocksToUpdate = $blocksToUpdate;
    }

    /**
     * @return mixed
     */
    public function getBlocksToAdd()
    {
        return $this->blocksToAdd;
    }

    /**
     * @param mixed $blocksToAdd
     */
    public function setBlocksToAdd($blocksToAdd)
    {
        $this->blocksToAdd = $blocksToAdd;
    }

    /**
     * @return mixed
     */
    public function getPageTypeDefaultPageID()
    {
        return $this->pageTypeDefaultPageID;
    }

    /**
     * @param mixed $pageTypeDefaultPageID
     */
    public function setPageTypeDefaultPageID($pageTypeDefaultPageID)
    {
        $this->pageTypeDefaultPageID = $pageTypeDefaultPageID;
    }

    public function getName()
    {
        return 'default';
    }

    public function getBatchHandle()
    {
        return 'update_page_type_defaults';
    }

}