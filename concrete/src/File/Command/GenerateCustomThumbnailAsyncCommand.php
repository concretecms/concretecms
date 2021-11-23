<?php

namespace Concrete\Core\File\Command;

class GenerateCustomThumbnailAsyncCommand extends FileCommand
{
    /** @var int */
    protected $maxWidth;
    /** @var int */
    protected $maxHeight;
    /** @var bool */
    protected $crop;

    public function __construct(int $fileID, int $maxWidth, int $maxHeight, bool $crop)
    {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->crop = $crop;
        parent::__construct($fileID);
    }

    /**
     * @return int
     */
    public function getFileID(): int
    {
        return $this->fileID;
    }

    /**
     * @param int $fileID
     * @return GenerateCustomThumbnailAsyncCommand
     */
    public function setFileID(int $fileID): GenerateCustomThumbnailAsyncCommand
    {
        $this->fileID = $fileID;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     * @return GenerateCustomThumbnailAsyncCommand
     */
    public function setMaxWidth(int $maxWidth): GenerateCustomThumbnailAsyncCommand
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     * @return GenerateCustomThumbnailAsyncCommand
     */
    public function setMaxHeight(int $maxHeight): GenerateCustomThumbnailAsyncCommand
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCrop(): bool
    {
        return $this->crop;
    }

    /**
     * @param bool $crop
     * @return GenerateCustomThumbnailAsyncCommand
     */
    public function setCrop(bool $crop): GenerateCustomThumbnailAsyncCommand
    {
        $this->crop = $crop;
        return $this;
    }

    public static function getHandler(): string
    {
        return GenerateCustomThumbnailCommandHandler::class;
    }

}
