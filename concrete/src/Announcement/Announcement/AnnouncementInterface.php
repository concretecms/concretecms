<?php

namespace Concrete\Core\Announcement\Announcement;

use Concrete\Core\Announcement\Slide\SlideInterface;

interface AnnouncementInterface extends \JsonSerializable
{

    public function getComponent(): string;

    /**
     * @return SlideInterface[]
     */
    public function getSlides(): array;


}
