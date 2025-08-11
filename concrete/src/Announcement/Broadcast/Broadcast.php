<?php

namespace Concrete\Core\Announcement\Broadcast;

use Concrete\Core\Announcement\Announcement\AnnouncementInterface;

class Broadcast implements BroadcastInterface
{

    /**
     * @var AnnouncementInterface[]
     */
    protected $announcements = [];

    public function __construct($announcements = [])
    {
        $this->announcements = $announcements;
    }

    public function addAnnouncement(AnnouncementInterface $announcement)
    {
        $this->announcements[] = $announcement;
    }

    public function addAnnouncements(array $announcements)
    {
        foreach ($announcements as $announcement) {
            $this->addAnnouncement($announcement);
        }
    }

    /**
     * @return AnnouncementInterface[]
     */
    public function getAnnouncements(): array
    {
        return $this->announcements;
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
            'announcements' => $this->getAnnouncements(),
        ];
    }
}
