<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\Notification\Events\Topic\ConcreteTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class ThumbnailGeneratedEvent extends AbstractConcreteEvent implements SubscribableEventInterface
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

    public function createTopic(): TopicInterface
    {
        return static::getTopicForSubscribing();
    }

    public static function getTopicForSubscribing(): TopicInterface
    {
        return new ConcreteTopic('/thumbnail_generated');
    }

    public function getEventData(): array
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