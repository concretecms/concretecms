<?php

namespace Concrete\Core\File\ExternalFileProvider;

class ExternalSearchRequest
{
    /** @var string */
    protected $searchTerm;
    /** @var string */
    protected $fileType;
    /** @var string */
    protected $orderBy;
    /** @var string */
    protected $orderByDirection;
    /** @var int */
    protected $currentPage;
    /** @var int */
    protected $itemsPerPage;

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     * @return ExternalSearchRequest
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     * @return ExternalSearchRequest
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return ExternalSearchRequest
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderByDirection()
    {
        return $this->orderByDirection;
    }

    /**
     * @param string $orderByDirection
     * @return ExternalSearchRequest
     */
    public function setOrderByDirection($orderByDirection)
    {
        $this->orderByDirection = $orderByDirection;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return ExternalSearchRequest
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     * @return ExternalSearchRequest
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }



}