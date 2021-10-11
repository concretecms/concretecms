<?php

namespace Concrete\Core\File\Command;

class GeneratedThumbnailCommand extends FileCommand
{
    /**
     * @var int
     */
    protected $fileVersionID;

    /**
     * @var string
     */
    protected $thumbnailTypeHandle;

    public function __construct(int $fileID, int $fileVersionID, string $thumbnailTypeHandle)
    {
        $this->thumbnailTypeHandle = $thumbnailTypeHandle;
        $this->fileVersionID = $fileVersionID;
        parent::__construct($fileID);
    }

    /**
     * @return int
     */
    public function getFileVersionID(): int
    {
        return $this->fileVersionID;
    }

    /**
     * @return string
     */
    public function getThumbnailTypeHandle(): string
    {
        return $this->thumbnailTypeHandle;
    }

    public static function getHandler(): string
    {
        return GenerateThumbnailCommandHandler::class;
    }

}
