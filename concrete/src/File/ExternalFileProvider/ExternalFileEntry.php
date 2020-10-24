<?php

namespace Concrete\Core\File\ExternalFileProvider;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;
use DateTime;
use HtmlObject\Element;

/**
 * Class ExternalFileEntry
 *
 * This class contains all properties that are used by the vue file component.
 *
 * @package Concrete\Core\File\ExternalFileProvider
 */
class ExternalFileEntry implements \JsonSerializable
{
    /** @var int */
    protected $fID;
    /** @var string */
    protected $title;
    /** @var string */
    protected $thumbnailUrl;
    /** @var DateTime */
    protected $fvDateAdded;
    /** @var bool */
    protected $isFolder = false;
    /** @var int */
    protected $treeNodeID;

    /**
     * @return int
     */
    public function getFID()
    {
        return $this->fID;
    }

    /**
     * @param int $fID
     * @return ExternalFileEntry
     */
    public function setFID($fID)
    {
        $this->fID = $fID;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ExternalFileEntry
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string $thumbnailUrl
     * @return ExternalFileEntry
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFvDateAdded()
    {
        return $this->fvDateAdded;
    }

    /**
     * @param DateTime $fvDateAdded
     * @return ExternalFileEntry
     */
    public function setFvDateAdded($fvDateAdded)
    {
        $this->fvDateAdded = $fvDateAdded;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFolder()
    {
        return $this->isFolder;
    }

    /**
     * @param bool $isFolder
     * @return ExternalFileEntry
     */
    public function setIsFolder($isFolder)
    {
        $this->isFolder = $isFolder;
        return $this;
    }

    /**
     * @return int
     */
    public function getTreeNodeID()
    {
        return $this->treeNodeID;
    }

    /**
     * @param int $treeNodeID
     * @return ExternalFileEntry
     */
    public function setTreeNodeID($treeNodeID)
    {
        $this->treeNodeID = $treeNodeID;
        return $this;
    }

    public function getListingThumbnailImage()
    {

        $app = Application::getFacadeApplication();
        /** @var Repository $config */
        $config = $app->make(Repository::class);
        $img = new Element("img");
        $img->setAttribute("src", $this->getThumbnailUrl());
        $img->addClass("ccm-file-manager-list-thumbnail ccm-thumbnail-" . $config->get('concrete.file_manager.images.preview_image_size'));
        return (string)$img;
    }

    public function jsonSerialize()
    {
        return [
            "fID" => $this->getFID(),
            "resultsThumbnailImg" => $this->getListingThumbnailImage(),
            "title" => $this->getTitle(),
            "fvDateAdded" => $this->getFvDateAdded() instanceof DateTime ? $this->getFvDateAdded()->format('F d, Y g:i a') : null,
            "isFolder" => $this->isFolder(),
            "treeNodeID" => $this->getTreeNodeID()
        ];
    }

}