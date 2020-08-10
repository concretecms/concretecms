<?php

namespace Concrete\Core\Page\Type\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class UpdatePageTypeDefaultsCommand implements BatchableCommandInterface, QueueableCommand
{
    /**
     * @var int
     */
    protected $pageTypeDefaultPageID;

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
    protected $blocksToUpdate;

    /**
     * @var string
     */
    protected $blocksToAdd;

    public function __construct(int $pageTypeDefaultPageID, int $pageID, int $collectionVersionID, string $blocksToUpdate, string $blocksToAdd)
    {
        $this
            ->setPageTypeDefaultPageID($pageTypeDefaultPageID)
            ->setPageID($pageID)
            ->setCollectionVersionID($collectionVersionID)
            ->setBlocksToUpdate($blocksToUpdate)
            ->setBlocksToAdd($blocksToAdd)
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'update_page_type_defaults';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Bernard\Message::getName()
     */
    public function getName(): string
    {
        return 'default';
    }

    public function getPageTypeDefaultPageID(): int
    {
        return $this->pageTypeDefaultPageID;
    }

    /**
     * @return $this
     */
    public function setPageTypeDefaultPageID(int $pageTypeDefaultPageID): object
    {
        $this->pageTypeDefaultPageID = $pageTypeDefaultPageID;

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

    public function getBlocksToUpdate(): string
    {
        return $this->blocksToUpdate;
    }

    /**
     * @return $this
     */
    public function setBlocksToUpdate(string $blocksToUpdate): object
    {
        $this->blocksToUpdate = $blocksToUpdate;

        return $this;
    }

    public function getBlocksToAdd(): string
    {
        return $this->blocksToAdd;
    }

    /**
     * @return $this
     */
    public function setBlocksToAdd(string $blocksToAdd): object
    {
        $this->blocksToAdd = $blocksToAdd;

        return $this;
    }
}
