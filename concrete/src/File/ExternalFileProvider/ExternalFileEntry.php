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
    /** @var string */
    protected $size;
    /** @var int */
    protected $width;
    /** @var int */
    protected $height;
    /** @var DateTime */
    protected $fvDateAdded;
    /** @var bool */
    protected $isFolder = false;
    /** @var int */
    protected $treeNodeID;

    /**
     * @return int|null
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
     * @return string|null
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
     * @return string|null
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
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return ExternalFileEntry
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return ExternalFileEntry
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return ExternalFileEntry
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return DateTime|null
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
     * @return int|null
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

    public function getDetailThumbnailImage()
    {
        $img = new Element("img");
        $img->setAttribute("src", $this->getThumbnailUrl());
        $img->addClass("ccm-file-manager-detail-thumbnail");
        return (string)$img;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $file = [
            "fID" => $this->getFID(),
            "resultsThumbnailImg" => $this->getListingThumbnailImage(),
            "resultsThumbnailDetailImg" => $this->getDetailThumbnailImage(),
            "size" => $this->getSize(),
            "title" => $this->getTitle(),
            "fvDateAdded" => $this->getFvDateAdded() instanceof DateTime ? $this->getFvDateAdded()->format('F d, Y g:i a') : null,
            "isFolder" => $this->isFolder(),
            "treeNodeID" => $this->getTreeNodeID()
        ];

        if ($this->getWidth() && $this->getHeight()) {
            $file["attributes"] = [
                'width' => $this->getWidth(),
                'height' => $this->getHeight()
            ];
        }

        return $file;
    }

}