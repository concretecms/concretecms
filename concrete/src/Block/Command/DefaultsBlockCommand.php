<?php

namespace Concrete\Core\Block\Command;

abstract class DefaultsBlockCommand extends BlockCommand
{
    /**
     * @var int
     */
    protected $originalBlockID;

    /**
     * @var int
     */
    protected $originalPageID;

    /**
     * @var int
     */
    protected $originalCollectionVersionID;

    /**
     * @var string
     */
    protected $originalAreaHandle;

    /** @var bool */
    protected $forceDisplayOrder;

    public function __construct(
        int $originalBlockID,
        int $originalPageID,
        int $originalCollectionVersionID,
        string $originalAreaHandle,
        int $blockID,
        int $pageID,
        int $collectionVersionID,
        string $areaHandle,
        bool $forceDisplayOrder = false
    ) {
        parent::__construct($blockID, $pageID, $collectionVersionID, $areaHandle);
        $this
            ->setOriginalBlockID($originalBlockID)
            ->setOriginalPageID($originalPageID)
            ->setOriginalCollectionVersionID($originalCollectionVersionID)
            ->setOriginalAreaHandle($originalAreaHandle)
            ->setForceDisplayOrder($forceDisplayOrder)
        ;
    }

    public function getOriginalBlockID(): int
    {
        return $this->originalBlockID;
    }

    /**
     * @return $this
     */
    public function setOriginalBlockID(int $originalBlockID): object
    {
        $this->originalBlockID = $originalBlockID;

        return $this;
    }

    public function getOriginalPageID(): int
    {
        return $this->originalPageID;
    }

    /**
     * @return $this
     */
    public function setOriginalPageID(int $originalPageID): object
    {
        $this->originalPageID = $originalPageID;

        return $this;
    }

    public function getOriginalCollectionVersionID(): int
    {
        return $this->originalCollectionVersionID;
    }

    /**
     * @return $this
     */
    public function setOriginalCollectionVersionID(int $originalCollectionVersionID): object
    {
        $this->originalCollectionVersionID = $originalCollectionVersionID;

        return $this;
    }

    public function getOriginalAreaHandle(): string
    {
        return $this->originalAreaHandle;
    }

    /**
     * @return $this
     */
    public function setOriginalAreaHandle(string $originalAreaHandle): object
    {
        $this->originalAreaHandle = $originalAreaHandle;

        return $this;
    }

    /**
     * @return bool
     */
    public function getForceDisplayOrder(): bool
    {
        return $this->forceDisplayOrder;
    }

    /**
     * @param bool $forceDisplayOrder
     * @return $this
     */
    public function setForceDisplayOrder(bool $forceDisplayOrder): self
    {
        $this->forceDisplayOrder = $forceDisplayOrder;

        return $this;
    }


}
