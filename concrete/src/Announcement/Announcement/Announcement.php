<?php

namespace Concrete\Core\Announcement\Announcement;

use Concrete\Core\Announcement\Slide\SlideInterface;

class Announcement implements AnnouncementInterface
{

    /**
     * @var SlideInterface
     */
    protected $slides = [];

    /**
     * @var string
     */
    protected $announcementHandle;

    public function __construct(string $announcementHandle, $slides = [])
    {
        $this->announcementHandle = $announcementHandle;
        $this->slides = $slides;
    }

    public function addSlide(SlideInterface $slide)
    {
        $this->slides[] = $slide;
    }

    public function addSlides(array $slides)
    {
        foreach ($slides as $slide) {
            $this->addSlide($slide);
        }
    }

    /**
     * @return string
     */
    public function getAnnouncementHandle(): string
    {
        return $this->announcementHandle;
    }

    /**
     * @return SlideInterface[]
     */
    public function getSlides(): array
    {
        return $this->slides;
    }

    public function getComponent(): string
    {
        return 'concrete-announcement-modal';
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'component' => $this->getComponent(),
            'handle' => $this->getAnnouncementHandle(),
            'slides' => $this->getSlides(),
        ];
    }
}
