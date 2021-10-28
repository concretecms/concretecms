<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;

class ThumbnailGenerated implements EventInterface
{
    /**
     * @var FileVersion
     */
    protected $fileVersion;

    /**
     * @var ThumbnailTypeVersion
     */
    protected $thumbnailTypeVersion;

    /**
     * @param FileVersion $fileVersion
     * @param ThumbnailTypeVersion $thumbnailTypeVersion
     */
    public function __construct(
        FileVersion $fileVersion,
        ThumbnailTypeVersion $thumbnailTypeVersion
    )
    {
        $this->fileVersion = $fileVersion;
        $this->thumbnailTypeVersion = $thumbnailTypeVersion;
    }

    public function getEvent(): string
    {
        return 'ThumbnailGenerated';
    }

    public function getData(): array
    {
        return [
            'fileId' => $this->fileVersion->getFileID(),
            'fileVersionId' => $this->fileVersion->getFileVersionID(),
            'thumbnailTypeHandle' => $this->thumbnailTypeVersion->getHandle(),
            'fileName' => $this->fileVersion->getFileName(),
            'thumbnailUrl' => $this->fileVersion->getThumbnailURL($this->thumbnailTypeVersion)
        ];
    }

}