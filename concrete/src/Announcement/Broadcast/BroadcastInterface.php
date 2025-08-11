<?php

namespace Concrete\Core\Announcement\Broadcast;

use Concrete\Core\Announcement\Announcement\AnnouncementInterface;

interface BroadcastInterface extends \JsonSerializable
{

    /**
     * @return AnnouncementInterface[]
     */
    public function getAnnouncements(): array;


}
