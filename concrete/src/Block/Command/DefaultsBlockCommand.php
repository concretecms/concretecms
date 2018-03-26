<?php

namespace Concrete\Core\Block\Command;

abstract class DefaultsBlockCommand extends BlockCommand
{

    protected $originalBlockID;

    protected $originalPageID;

    protected $originalCollectionVersionID;

    protected $originalAreaHandle;

    public function __construct(
        $originalBlockID,
        $originalPageID,
        $originalCollectionVersionID,
        $originalAreaHandle,
        $blockID,
        $pageID,
        $collectionVersionID,
        $areaHandle
    ) {
        $this->originalPageID = $originalPageID;
        $this->originalBlockID = $originalBlockID;
        $this->originalCollectionVersionID = $originalCollectionVersionID;
        $this->originalAreaHandle = $originalAreaHandle;
        parent::__construct($blockID, $pageID, $collectionVersionID, $areaHandle);
    }

    /**
     * @return mixed
     */
    public function getOriginalBlockID()
    {
        return $this->originalBlockID;
    }

    /**
     * @param mixed $originalBlockID
     */
    public function setOriginalBlockID($originalBlockID)
    {
        $this->originalBlockID = $originalBlockID;
    }

    /**
     * @return mixed
     */
    public function getOriginalPageID()
    {
        return $this->originalPageID;
    }

    /**
     * @param mixed $originalPageID
     */
    public function setOriginalPageID($originalPageID)
    {
        $this->originalPageID = $originalPageID;
    }

    /**
     * @return mixed
     */
    public function getOriginalCollectionVersionID()
    {
        return $this->originalCollectionVersionID;
    }

    /**
     * @param mixed $originalCollectionVersionID
     */
    public function setOriginalCollectionVersionID($originalCollectionVersionID)
    {
        $this->originalCollectionVersionID = $originalCollectionVersionID;
    }

    /**
     * @return mixed
     */
    public function getOriginalAreaHandle()
    {
        return $this->originalAreaHandle;
    }

    /**
     * @param mixed $originalAreaHandle
     */
    public function setOriginalAreaHandle($originalAreaHandle)
    {
        $this->originalAreaHandle = $originalAreaHandle;
    }


}